<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Accessor;

use TimurFlush\Auth\Accessor\PhpSession\AdapterInterface;
use TimurFlush\Auth\Cookies\CookiesInterface;
use TimurFlush\Auth\Event\ManagerInterface as EventsManager;
use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Session\RepositoryInterface;
use TimurFlush\Auth\Session\SessionInterface;
use TimurFlush\Auth\User\RepositoryInterface as UserRepository;
use TimurFlush\Auth\User\UserInterface;
use TimurFlush\Auth\Session\RepositoryInterface as SessionRepository;

class PhpSessionInterface extends AccessorInterfaceAbstract implements StatefulAccessorInterface
{
    protected const RESOLVING_VIA_PHP_SESSION = 0;
    protected const RESOLVING_VIA_COOKIES = 1;

    /**
     * @var string
     */
    protected string $moduleName;

    /**
     * @var \TimurFlush\Auth\Accessor\PhpSession\AdapterInterface
     */
    protected AdapterInterface $adapter;

    /**
     * @var \TimurFlush\Auth\User\RepositoryInterface
     */
    protected UserRepository $userRepository;

    /**
     * @var \TimurFlush\Auth\Session\RepositoryInterface
     */
    protected SessionRepository $sessionRepository;

    /**
     * @var \TimurFlush\Auth\Cookies\CookiesInterface
     */
    protected CookiesInterface $cookies;

    /**
     * PhpSessionInterface constructor.
     *
     * @param string                                                $module            A module name to save sessions with prefix.
     * @param \TimurFlush\Auth\Accessor\PhpSession\AdapterInterface $adapter
     * @param \TimurFlush\Auth\User\RepositoryInterface             $userRepository
     * @param \TimurFlush\Auth\Session\RepositoryInterface          $sessionRepository
     * @param \TimurFlush\Auth\Event\ManagerInterface               $eventsManager
     */
    public function __construct(
        string $module,
        AdapterInterface $adapter,
        UserRepository $userRepository,
        RepositoryInterface $sessionRepository,
        CookiesInterface $cookies,
        EventsManager $eventsManager = null
    ) {
        $adapter->setModuleName($module);
        $cookies->setModuleName($module);

        $this->moduleName = $module;
        $this->adapter = $adapter;
        $this->userRepository = $userRepository;
        $this->sessionRepository = $sessionRepository;
        $this->cookies = $cookies;

        if ($eventsManager) {
            $this->eventsManager = $eventsManager;
        }
    }


    protected function resolve(int $method, $userId, $sessionId, string $rememberToken = null)
    {
        if (
            !in_array($method, [
                static::RESOLVING_VIA_PHP_SESSION,
                static::RESOLVING_VIA_COOKIES
            ])
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
            if (!is_int($userId)) {
                $fail = true;
                break;
            }
            if (!is_string($sessionId)) {
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
             * Check the user for blocking and activation
             */
            if (
                $matchedUser->getBanStatus() === true ||
                $matchedUser->getActivationStatus() === false
            ) {
                $fail = $needRevoke = true;
                break;
            }

            $eventData = [
                'user' => $matchedUser,
                'session' => $matchedSession,
                'module' => $this->moduleName,
                'resolvingType' => $method
            ];

            if (
                $this->fireEvent('customChecksOnResolving', $this, $eventData) === false
            ) {

            }
        } while (false);

        /**
         * In fail case, to avoid overhead we need to remove the data
         * from php session or cookies depending on the method
         */
        if ($fail) {
            if ($method === static::RESOLVING_VIA_PHP_SESSION) {
                $this->adapter->remove();
            } elseif ($method === static::RESOLVING_VIA_COOKIES) {
                $this->cookies->remove();
            }

            if ($needRevoke && $matchedSession) {
                $matchedSession->revoke();
                $this->sessionRepository->save($matchedSession);
            }
        }
    }

    public function resolveViaPhpSession(): bool
    {
        $phpSession = $this->adapter->get();

        if (!isset($phpSession['userId'], $phpSession['sessionId'])) {
            return false;
        }

        $this->resolve(
            static::RESOLVING_VIA_PHP_SESSION,
            $phpSession['userId'],
            $phpSession['sessionId']
        );

        return;


        if (
            !is_int($userId) ||
            !is_string($sessionId)
        ) {
            $this->adapter->remove();
            return false;
        } // Z

        $session = $this->sessionRepository->findById($sessionId); // Z

        if (($session instanceof SessionInterface) === false) { // Z
            $this->cookies->remove();
            return false;
        }

        $expiresAt = $session->getExpiresAt(); // Z

        // Is revoked?
        if ($session->isRevoked()) { // Z
            $this->cookies->remove();
            return false;
        }

        // Is past?
        if ($expiresAt->isPast()) { // Z
            $session->revoke();

            $this->sessionRepository->save($session);

            $this->cookies->remove();
            return false;
        }

        $userId = $session->getUserId(); // Z

        if ($userId === null) { // Z
            $session->revoke();

            $this->sessionRepository->save($session);

            $this->cookies->remove();
            return false;
        }

        $user = $this->userRepository->findById((int)$userId); // Z

        if (($user instanceof UserInterface) === false) { // Z
            $session->revoke();

            $this->sessionRepository->save($session);

            $this->cookies->remove();
            return false;
        }

        if (
            $user->getBanStatus() === true ||
            $user->getActivationStatus() === false
        ) { // Z
            $session->revoke();

            $this->sessionRepository->save($session);

            $this->cookies->remove();
            return false;
        }

        $eventData = [
            'user' => $user,
            'session' => $session,
            'module' => $this->moduleName
        ]; // Z

        if (
            $this->fireEvent('customChecksOnResolvingViaCookies', $this, $eventData) === false ||
            $this->fireEvent('customChecksOnResolving', $this, $eventData) === false
        ) { // Z
            $session->revoke();

            $this->sessionRepository->save($session);

            $this->cookies->remove();
            return false;
        }
    }

    public function resolveViaCookies(): bool
    {
        $cookies = $this->cookies->get();

        if ($cookies === null) {
            return false;
        } elseif (!is_array($cookies)) {
            $this->cookies->remove();
            return false;
        }

        // TODO : Complete this and think about the multi-accessor mode in $this->adapter & $this->cookies

        $sessionId = $cookies['sessionId'] ?? null; // Z
        $rememberToken = $cookies['rememberToken'] ?? null; // Z

        if (
            !is_string($sessionId) ||
            !is_string($rememberToken)
        ) {
            $this->cookies->remove();
            return false;
        } // Z

        $session = $this->sessionRepository->findById($sessionId); // Z

        if (($session instanceof SessionInterface) === false) { // Z
            $this->cookies->remove();
            return false;
        }

        $expiresAt = $session->getExpiresAt(); // Z

        // Is revoked?
        if ($session->isRevoked()) { // Z
            $this->cookies->remove();
            return false;
        }

        // Is past?
        if ($expiresAt->isPast()) { // Z
            $session->revoke();

            $this->sessionRepository->save($session);

            $this->cookies->remove();
            return false;
        }

        // Is remember tokens not equals?
        if ($rememberToken !== $session->getRememberToken()) {
            $session->revoke();

            $this->sessionRepository->save($session);

            $this->cookies->remove();
            return false;
        }

        $userId = $session->getUserId(); // Z

        if ($userId === null) { // Z
            $session->revoke();

            $this->sessionRepository->save($session);

            $this->cookies->remove();
            return false;
        }

        $user = $this->userRepository->findById((int)$userId); // Z

        if (($user instanceof UserInterface) === false) { // Z
            $session->revoke();

            $this->sessionRepository->save($session);

            $this->cookies->remove();
            return false;
        }

        if (
            $user->getBanStatus() === true ||
            $user->getActivationStatus() === false
        ) { // Z
            $session->revoke();

            $this->sessionRepository->save($session);

            $this->cookies->remove();
            return false;
        }

        $eventData = [
            'user' => $user,
            'session' => $session,
            'module' => $this->moduleName
        ]; // Z

        if (
            $this->fireEvent('customChecksOnResolvingViaCookies', $this, $eventData) === false ||
            $this->fireEvent('customChecksOnResolving', $this, $eventData) === false
        ) { // Z
            $session->revoke();

            $this->sessionRepository->save($session);

            $this->cookies->remove();
            return false;
        }

        $this->adapter->set(
            [
                'userId' => $user->getId(),
                'sessionId' => $session->getId()
            ]
        );

        $this->setUser($user);
        $this->setSession($session);

        return true;
    }

    /**
     * Get an unique session key.
     *
     * @return string
     */
    public function getSessionKey(): string
    {
        return join('_' , [
            'TF_Accessor',
            $this->moduleName,
            sha1(static::class)
        ]);
    }

    public function attemptLogin(array $credentials, bool $remember = false): bool
    {
        $user = $this->userRepository->findByCredentials($credentials);

        if ($user instanceof UserInterface && $user->checkCredentials($credentials)) {
            return $this->loginByUser($user, $remember);
        }

        return false;
    }

    public function loginByUser(UserInterface $user, bool $remember = false): bool
    {
        if ($this->fireEvent('beforeAuth', $this, ['user' => $user]) === false) {
            return false;
        }

        $session = $this->sessionRepository->createNewSession($user);

        $data = [
            'userId' => $user->getId(),
            'sessionId' => $session->getId()
        ];

        # Create php session
        $this->adapter->set($this->getSessionKey(), $data);

        # Create user session
        $this->sessionRepository->save($session);

        $this->setUser($user);
        $this->setSession($session);

        $this->fireEvent('afterAuth', $this, [
            'user' => $user,
            'session' => $session
        ]);

        return true;
    }

    public function loginById(int $userId, bool $remember = false): bool
    {
        $user = $this->userRepository->findById($userId);

        if ($user instanceof UserInterface) {
            return $this->loginByUser($user, $remember);
        }

        return false;
    }

    public function loginByUserOnce(UserInterface $user): bool
    {
        $this->setUser($user);

        return true;
    }

    public function loginByIdOnce(int $userId): bool
    {
        $user = $this->userRepository->findById($userId);

        if ($user instanceof UserInterface) {
            $this->setUser($user);
            return true;
        }

        return false;
    }

    public function isAuthViaRemember(): bool
    {
        if (isset($this->session)) {
            return $this->session->getRememberToken() !== null;
        }

        return false;
    }

    public function logout(): void
    {
        $user = null;
        $session = null;

        // delete cookie
        // TODO : Deleting from cookies

        // delete php session
        $this->adapter->remove($this->getSessionKey());

        // unlink a user from this class
        if (isset($this->user)) {
            $user = $this->user;
            unset($this->user);
        }

        // unlink a user session from this class
        if (isset($this->session)) {
            $session = $this->session;

            // revoke a session
            $this->session->setExpiresAt(null);

            // save a session
            $this->sessionRepository->save($this->session);

            unset($this->session);
        }

        $this->fireEvent('logout', $this, [
            'user' => $user,
            'session' => $session
        ]);
    }
}
