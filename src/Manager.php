<?php

declare(strict_types=1);

namespace TimurFlush\Auth;

use TimurFlush\Auth\Activation\ActivationInterface;
use TimurFlush\Auth\Activation\RepositoryInterface as ActivationRepository;
use TimurFlush\Auth\Event\Fireable;
use TimurFlush\Auth\Event\AfterRegister;
use TimurFlush\Auth\Event\BeforeRegister;
use TimurFlush\Auth\Event\NeedActivation;
use TimurFlush\Auth\Permission\SerializerInterface;
use TimurFlush\Auth\Policy\PolicyManager;
use TimurFlush\Auth\Role\RoleInterface;
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
     * @var  \TimurFlush\Auth\Permission\SerializerInterface
     */
    protected SerializerInterface $permissionsSerializer;

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
        $this->permissionsSerializer = $permissionsSerializer;

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
     * Returns a permissions serializer instance.
     *
     * @return SerializerInterface
     */
    public function getPermissionsSerializer(): SerializerInterface
    {
        return $this->permissionsSerializer;
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
     * @throws Exception If the 'activate' argument is not a Closure object or boolean.
     */
    public function register(array $credentials, $activate = false): bool
    {
        if (($activate instanceof Closure) === false && !is_bool($activate)) {
            throw new Exception("The 'activate' argument must be a Closure object or boolean.");
        }

        if ($this->fireEvent(new BeforeRegister($credentials)) === false) {
            return false;
        }

        if ($activate instanceof Closure) {
            $activate = $activate();
        }

        $user = $this->userRepository->createNewUser($credentials, $activate);

        $this->userRepository->save($user);

        $this->fireEvent(new AfterRegister($user));

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function attemptActivate(int $userId, string $activationId): bool
    {
        $activation = $this->activationRepository->find($activationId, $userId);

        if ($activation instanceof ActivationInterface) {
            $user = $this->userRepository->findById($userId);

            if ($user instanceof UserInterface) {
                return $this->activateByUser($user);
            }

            $this->activationRepository->delete($activation);
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
        $activation = $this->activationRepository->create($user);

        $this->activationRepository->save($activation);

        $this->fireEvent(new NeedActivation($user, $activation));

        return $activation;
    }
}
