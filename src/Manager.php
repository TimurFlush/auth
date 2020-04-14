<?php

declare(strict_types=1);

namespace TimurFlush\Auth;

use TimurFlush\Auth\Activation\ActivationInterface;
use TimurFlush\Auth\Activation\RepositoryInterface as ActivationRepository;
use TimurFlush\Auth\Event\Fireable;
use TimurFlush\Auth\Event\AfterRegister;
use TimurFlush\Auth\Event\BeforeRegister;
use TimurFlush\Auth\Event\NeedActivation;
use TimurFlush\Auth\Serializer\SerializerInterface;
use TimurFlush\Auth\Role\RepositoryInterface as RoleRepository;
use TimurFlush\Auth\User\RepositoryInterface as UserRepository;
use TimurFlush\Auth\User\UserInterface;
use Closure;
use Phalcon\Events\ManagerInterface as EventsManager;

class Manager implements ManagerInterface
{
    use Fireable;

    public const DEFAULT_SQL_DATE = 'Y-m-d H:i:s.uO';

    /**
     * @var string
     */
    protected string $sqlDateFormat;

    /**
     * @var \TimurFlush\Auth\User\RepositoryInterface
     */
    protected UserRepository $userRepository;

    /**
     * @var \TimurFlush\Auth\Activation\RepositoryInterface
     */
    protected ActivationRepository $activationRepository;

    /**
     * @var \TimurFlush\Auth\Role\RepositoryInterface
     */
    protected RoleRepository $roleRepository;

    /**
     * @var  \TimurFlush\Auth\Serializer\SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * Manager constructor.
     *
     * @param UserRepository        $userRepository
     * @param ActivationRepository  $activationRepository
     * @param RoleRepository        $roleRepository
     * @param SerializerInterface   $permissionsSerializer
     * @param EventsManager|null    $eventsManager
     */
    public function __construct(
        UserRepository $userRepository,
        ActivationRepository $activationRepository,
        RoleRepository $roleRepository,
        SerializerInterface $permissionsSerializer,
        ?EventsManager $eventsManager = null
    ) {
        $this->userRepository = $userRepository;
        $this->activationRepository = $activationRepository;
        $this->roleRepository = $roleRepository;
        $this->serializer = $permissionsSerializer;

        if ($eventsManager !== null) {
            $this->setEventsManager($eventsManager);
        }
    }

    /**
     * Returns a user repository instance.
     *
     * @return UserRepository
     */
    public function getUserRepository(): UserRepository
    {
        return $this->userRepository;
    }

    /**
     * Returns an activation repository instance.
     *
     * @return ActivationRepository
     */
    public function getActivationRepository(): ActivationRepository
    {
        return $this->activationRepository;
    }

    /**
     * Returns a role repository instance.
     *
     * @return RoleRepository
     */
    public function getRoleRepository(): RoleRepository
    {
        return $this->roleRepository;
    }

    /**
     * Returns a serializer instance.
     *
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function setSqlDateFormat(string $format)
    {
        $this->sqlDateFormat = $format;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSqlDateFormat(): string
    {
        return isset($this->sqlDateFormat)
            ? $this->sqlDateFormat
            : self::DEFAULT_SQL_DATE;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \TimurFlush\Auth\Exception If the 'activate' argument is not a Closure object or boolean.
     * @throws \TimurFlush\Auth\Exception If unable to register a user
     */
    public function register(array $credentials, $activate = false): UserInterface
    {
        if (($activate instanceof Closure) === false && !is_bool($activate)) {
            throw new Exception("The 'activate' argument must be a Closure object or boolean.");
        }

        if ($this->fireEvent(new BeforeRegister($credentials)) === false) {
            throw new Exception("Unable to register a user, because the 'beforeRegister' event returned a false");
        }

        if ($activate instanceof Closure) {
            $activate = $activate();
        }

        $user = $this->userRepository->createNewUser($credentials, $activate);

        $this->userRepository->save($user);

        $this->fireEvent(new AfterRegister($user));

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function attemptActivate(int $userId, string $activationId): bool
    {
        $activation = $this->activationRepository->find($activationId, $userId);

        if ($activation !== null) {
            $user = $this->userRepository->findById($userId);

            if ($user !== null) {
                if ($this->activateByUser($user)) {
                    $this->activationRepository->delete($activation);
                    return true;
                }
            }
        }

        return false;
    }

    public function activateByCredentials(array $credentials)
    {
        $user = $this->userRepository->findByCredentials($credentials);

        if ($user instanceof UserInterface) {
            return $this->activateByUser($user);
        }

        return false;
    }

    public function activateByUserId(int $userId): bool
    {
        $user = $this->userRepository->findById($userId);

        if ($user instanceof UserInterface) {
            return $this->activateByUser($user);
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function activateByUser(UserInterface $user): bool
    {
        $user->setActivationStatus(true);
        $this->userRepository->save($user);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function createActivation(UserInterface $user): ActivationInterface
    {
        $activation = $this->activationRepository->createNewActivation($user);

        $this->activationRepository->save($activation);

        $this->fireEvent(new NeedActivation($user, $activation));

        return $activation;
    }
}
