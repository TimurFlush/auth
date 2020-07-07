<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Accessor;

use TimurFlush\Auth\Accessor\PhpSession\CookieOptions;
use TimurFlush\Auth\Exception\InvalidArgumentException;
use Phalcon\Events\ManagerInterface as EventsManager;
use TimurFlush\Auth\Event\{
    BeforeAuth,
    AfterAuth,
    BeforeResolving,
    AfterResolving,
    FailedChecker,
    Logout
};
use TimurFlush\Auth\Session\{
    RepositoryInterface as SessionRepository,
    SessionInterface
};
use TimurFlush\Auth\User\{
    RepositoryInterface as UserRepository,
    UserInterface
};
use TimurFlush\Auth\Checker\Credentials;
use Phalcon\Session\ManagerInterface as PhalconSession;
use Phalcon\Http\Response\CookiesInterface as PhalconCookies;
use JsonException;

class PhpSession extends AccessorAbstract implements StatefulAccessorInterface
{
    protected const RESOLVING_VIA_PHP_SESSION = 0;
    protected const RESOLVING_VIA_COOKIES = 1;

    protected string $moduleName;

    protected PhalconSession $phalconSession;

    protected UserRepository $userRepository;

    protected SessionRepository $sessionRepository;

    protected PhalconCookies $phalconCookies;

    /**
     * @var \TimurFlush\Auth\Checker\OptionalCheckerInterface[]
     */
    protected array $checkers = [];

    protected CookieOptions $cookieOptions;

    /**
     * PhpSession constructor.
     *
     * @param string                                                $module            A module name to save sessions and cookies with prefix.
     * @param \Phalcon\Session\ManagerInterface                     $phalconSession
     * @param \Phalcon\Http\Response\CookiesInterface               $phalconCookies
     * @param \TimurFlush\Auth\User\RepositoryInterface             $userRepository
     * @param \TimurFlush\Auth\Session\RepositoryInterface          $sessionRepository
     * @param \TimurFlush\Auth\Checker\OptionalCheckerInterface[]   $checkers
     * @param \Phalcon\Events\ManagerInterface                      $eventsManager
     * @param \TimurFlush\Auth\Accessor\PhpSession\CookieOptions    $cookieOptions     The custom cookie's options.
     */
    public function __construct(
        string $module,
        PhalconSession $phalconSession,
        PhalconCookies $phalconCookies,
        UserRepository $userRepository,
        SessionRepository $sessionRepository,
        array $checkers = [],
        ?EventsManager $eventsManager = null,
        ?CookieOptions $cookieOptions = null
    ) {
        $this->moduleName = $module;
        $this->phalconSession = $phalconSession;
        $this->phalconCookies = $phalconCookies;
        $this->userRepository = $userRepository;
        $this->sessionRepository = $sessionRepository;
        $this->checkers = $checkers;

        if ($eventsManager !== null) {
            $this->setEventsManager($eventsManager);
        }

        if ($cookieOptions !== null) {
            $this->setCookieOptions($cookieOptions);
        }
    }

    /**
     * @inheritDoc
     */
    public function isAuth(): bool
    {
        $this->resolveIfNeed();

        return parent::isAuth();
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException Please see the method `static::resolveViaPhpSession()`
     * @throws InvalidArgumentException Please see the method `static::resolveViaCookies()`
     */
    public function getUser(): ?UserInterface
    {
        if (isset($this->user)) {
            return $this->user;
        }

        $this->resolveIfNeed();

        return isset($this->user)
            ? $this->user
            : null;
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException
     */
    public function getSession(): ?SessionInterface
    {
        if (isset($this->session)) {
            return $this->session;
        }

        $this->resolveIfNeed();

        return isset($this->session)
            ? $this->session
            : null;

    /**
     * @param bool $force
     *
     * @return void
     */
    protected function resolveIfNeed(bool $force = false): void
    {
        if (isset($this->user)) {
            return;
        }

        $isResolved = $this->resolveViaPhpSession();

        if (!$isResolved) {
            $this->resolveViaCookies();
        }
    }

    /**
     * Resolve a user.
     *
     * @param int         $method
     * @param mixed       $userId
     * @param mixed       $sessionId
     * @param string|null $rememberToken
     *
     * @return bool
     *
     * @throws InvalidArgumentException If specified an unknown resolving method
     */
    protected function _doLowResolve(int $method, $userId, $sessionId, $rememberToken = null): bool
    {
        if (
            $method !== static::RESOLVING_VIA_PHP_SESSION &&
            $method !== static::RESOLVING_VIA_COOKIES
        ) {
            throw new InvalidArgumentException('Unknown resolving method');
        }

        $fail = false;
        $needRevoke = false;

        /**
         * @var UserInterface|null $matchedUser
         */
        $matchedUser = null;

        /**
         * @var SessionInterface|null $matchedSession
         */
        $matchedSession = null;

        do {
            /**
             * Check data types
             */
            if (!is_int($userId) || !is_string($sessionId)) {
                $fail = true;
                break;
            }

            $matchedSession = $this->sessionRepository->findById($sessionId);

            /**
             * If the session does not exist in the repository
             */
            if ($matchedSession === null) {
                $fail = true;
                break;
            }

            /**
             * Check to see if the session was revoked earlier
             */
            if ($matchedSession->isRevoked()) {
                $fail = true;
                break;
            }

            $expiresAt = $matchedSession->getExpiresAt();

            /**
             * Check to see if the session has expired earlier
             */
            if ($expiresAt !== null && $expiresAt->isPast()) {
                $fail = $needRevoke = true;
                break;
            }

            /**
             * Check the tokens if the resolving is through a cookie.
             */
            if (
                $method === static::RESOLVING_VIA_COOKIES &&
                (
                    $rememberToken === null ||
                    $rememberToken !== $matchedSession->getRememberToken()
                )
            ) {
                $fail = $needRevoke = true;
                break;
            }

            $matchedUser = $this->userRepository->findById($userId);

            /**
             * Check to see if this user exists
             */
            if ($matchedUser === null) {
                $fail = $needRevoke = true;
                break;
            }

            /**
             * Check the user for some restrictions via checkers
             */
            foreach ($this->checkers as $checker) {
                if ($checker->onValidation($matchedUser) === false) {
                    $fail = $needRevoke = true;

                    $this->fireEvent(
                        new FailedChecker(
                            $checker,
                            $matchedUser,
                            FailedChecker::ON_VALIDATION,
                            $matchedSession
                        )
                    );
                    break;
                }
            }

            /**
             * Check the user for some restrictions via event
             */
            if (
                $this->fireEvent(
                    new BeforeResolving(
                        $matchedUser,
                        $matchedSession,
                        $this,
                        $method
                    )
                ) === false
            ) {
                $fail = $needRevoke = true;
                break;
            }

            $this->fireEvent(
                new AfterResolving(
                    $matchedUser,
                    $matchedSession,
                    $this,
                    $method
                )
            );
        } while (false);

        /**
         * In fail case, to avoid overhead we need to remove the data
         * from php session or cookies depending on the method
         */
        if ($fail) {
            if ($method === static::RESOLVING_VIA_PHP_SESSION) {
                $this->phalconSession->remove($this->getSessionKey());
            }

            $this->phalconCookies->delete($this->getCookiesKey());

            if ($needRevoke && $matchedSession) {
                $matchedSession->revoke();
                $this->sessionRepository->save($matchedSession);
            }

            return false;
        }

        $this
            ->setUser($matchedUser)
            ->setSession($matchedSession);

        return true;
    }

    /**
     * @throws InvalidArgumentException Please see the method `static::resolve()`
     */
    protected function resolveViaPhpSession(): bool
    {
        $session = $this->phalconSession->get($this->getSessionKey());

        if (!isset($session['userId'], $session['sessionId'])) {
            return false;
        }

        return $this->_doLowResolve(
            static::RESOLVING_VIA_PHP_SESSION,
            $session['userId'],
            $session['sessionId']
        );
    }

    /**
     * @throws InvalidArgumentException Please see the method `static::resolve()`
     */
    protected function resolveViaCookies(): bool
    {
        $cookies = $this
            ->phalconCookies
            ->get($this->getCookiesKey())
            ->getValue();

        if (!is_string($cookies)) {
            return false;
        }

        try {
            $cookies = json_decode($cookies, true, 2, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $this->phalconCookies->delete($this->getCookiesKey());
            return false;
        }

        if (!isset($cookies['userId'], $cookies['sessionId'], $cookies['rememberToken'])) {
            $this->phalconCookies->delete($this->getCookiesKey());
            return false;
        }

        return $this->_doLowResolve(
            static::RESOLVING_VIA_COOKIES,
            $cookies['userId'],
            $cookies['sessionId'],
            $cookies['rememberToken']
        );
    }

    /**
     * Sets cookie's options.
     */
    public function setCookieOptions(CookieOptions $cookieOptions): void
    {
        $this->cookieOptions = $cookieOptions;
    }

    /**
     * Returns cookie's options.
     */
    public function getCookieOptions(): CookieOptions
    {
        return $this->cookieOptions ??= new CookieOptions();
    }

    /**
     * {@inheritDoc}
     */
    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    /**
     * {@inheritDoc}
     */
    public function attemptLogin(
        array $credentials,
        bool $remember = false,
        array $extraCheckers = [],
        bool $replaceCheckers = false
    ): bool
    {
        $user = $this->userRepository->findByCredentials($credentials);

        if ($user instanceof UserInterface) {
            $credentialsChecker = new Credentials($credentials);
            $result = $credentialsChecker->onAuthentication($user);

            if ($result === true) {
                return $this->loginByUser($user, $remember, $extraCheckers, $replaceCheckers);
            } else {
                $this->fireEvent(
                    new FailedChecker(
                        $credentialsChecker,
                        $user,
                        FailedChecker::ON_AUTHENTICATION
                    )
                );
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function loginByUser(
        UserInterface $user,
        bool $remember = false,
        array $extraCheckers = [],
        bool $replaceCheckers = false
    ): bool
    {
        $checkers = $this->checkers;

        if ($replaceCheckers === true) {
            $this->checkers = [];
        }

        array_unshift($checkers, ...$extraCheckers);

        /**
         * First: we need to check a user on restrictions via checkers
         */
        foreach ($checkers as $checker) {
            if ($checker->onAuthentication($user) === false) {
                $this->fireEvent(
                    new FailedChecker(
                        $checker,
                        $user,
                        FailedChecker::ON_AUTHENTICATION
                    )
                );
                return false;
            }
        }

        /**
         * Second: we need to check a user on restrictions via event
         */
        if ($this->fireEvent(new BeforeAuth($this, $user)) === false) {
            return false;
        }

        $session = $this->sessionRepository->createNewSession($user, $remember);

        $data = [
            'userId' => $user->getId(),
            'sessionId' => $session->getId()
        ];

        if ($remember) {
            $data['rememberToken'] = $session->getRememberToken();

            $cookieOptions = $this->getCookieOptions();

            $this->phalconCookies->set(
                $this->getCookiesKey(),
                json_encode($data, JSON_THROW_ON_ERROR, 2),
                time() + 86400 * 365 * 5,
                $cookieOptions->getPath(),
                $cookieOptions->isSecure(),
                $cookieOptions->getDomain(),
                $cookieOptions->isHttpOnly(),
                $cookieOptions->getCustomOptions()
            );
        }

        # Create php session
        $this->phalconSession->set($this->getSessionKey(), $data);

        # Create user session
        $this->sessionRepository->save($session);

        $this
            ->setUser($user)
            ->setSession($session);

        $this->fireEvent(new AfterAuth($this, $user, $session));

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function loginById(
        int $userId,
        bool $remember = false,
        array $extraCheckers = [],
        bool $replaceCheckers = false
    ): bool
    {
        $user = $this->userRepository->findById($userId);

        if ($user instanceof UserInterface) {
            return $this->loginByUser($user, $remember, $extraCheckers, $replaceCheckers);
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function loginByUserOnce(UserInterface $user, array $extraCheckers = []): bool
    {
        if ($this->fireEvent(new BeforeAuth($this, $user, true)) === false) {
            return false;
        }

        foreach ($extraCheckers as $checker) {
            if ($checker->onAuthentication($user) === false) {
                $this->fireEvent(
                    new FailedChecker(
                        $checker,
                        $user,
                        FailedChecker::ON_AUTHENTICATION
                    )
                );
                break;
            }
        }

        $this
            ->setUser($user)
            ->setSession(null);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function loginByIdOnce(int $userId, array $extraCheckers = []): bool
    {
        $user = $this->userRepository->findById($userId);

        if ($user !== null) {
            return $this->loginByUserOnce($user, $extraCheckers);
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isAuthViaRemember(): bool
    {
        if (isset($this->session)) {
            return $this->session->getRememberToken() !== null;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isAuthViaOnce(): bool
    {
        return isset($this->user) && !isset($this->session);
    }

    /**
     * {@inheritDoc}
     */
    public function logout(): void
    {
        $user = null;
        $session = null;

        // delete cookie
        $this->phalconCookies->delete($this->getCookiesKey());

        // delete php session
        $this->phalconSession->remove($this->getSessionKey());

        // unlink a user from this class
        if (isset($this->user)) {
            $user = $this->user;
            unset($this->user);
        }

        // unlink a user session from this class
        if (isset($this->session)) {
            $session = $this->session;

            // revoke a session
            $this->session->revoke();

            // save a session
            $this->sessionRepository->save($this->session);

            unset($this->session);
        }

        if (isset($user)) {
            $this->fireEvent(new Logout($this, $user, $session));
        }
    }

    /**
     * Returns an unique session key for this accessor.
     */
    protected function getSessionKey(): string
    {
        return 'TFAccessor-' . $this->moduleName;
    }

    /**
     * Returns an unique cookie key for this accessor.
     */
    protected function getCookiesKey(): string
    {
        return $this->getCookieOptions()->getPrefixedName() . '-' . $this->moduleName;
    }
}
