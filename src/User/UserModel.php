<?php

declare(strict_types=1);

namespace TimurFlush\Auth\User;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\ModelInterface;
use TimurFlush\Auth\Exception;
use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Hashing\HashingLocator;
use TimurFlush\Auth\Manager;
use TimurFlush\Auth\Serializer\SerializerAwareInterface;
use TimurFlush\Auth\Serializer\SerializerInterface;
use TimurFlush\Auth\Policy\PolicyExecutorTrait;
use TimurFlush\Auth\Role\RoleInterface;
use TimurFlush\Auth\Exception\UnsafeException;

class UserModel extends Model implements UserInterface, SerializerAwareInterface
{
    use PolicyExecutorTrait;

    /**
     * @Column(type='biginteger', nullable=false)
     * @Primary
     * @Identity
     */
    protected ?int $id = null;

    /**
     * @Column(type='varchar', nullable=true)
     */
    protected ?string $password = null;

    /**
     * @Column(type='bool', nullable=true)
     */
    protected ?bool $ban_status = null;

    /**
     * @Column(type='bool', nullable=true)
     */
    protected ?bool $activation_status = null;

    /**
     * @Column(type='varchar', nullable=true)
     */
    protected ?string $api_token = null;

    /**
     * @Column(type='text', nullable=true)
     * @var string|array
     */
    protected $roles;

    /**
     * @Column(type='text', nullable=true)
     * @var string|array|null
     */
    protected $permissions;

    /**
     * @var \TimurFlush\Auth\Serializer\SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * Initialize method.
     */
    public function initialize(): void
    {
        $this->setSource('users');
        $this->useDynamicUpdate(true);
    }

    public function onConstruct()
    {
        /**
         * Set up the permissions serializer.
         */
        $this->serializer = $this
            ->getDI()
            ->getShared('authManager')
            ->getSerializer();
    }

    public function afterFetch()
    {
        if (!empty($this->roles)) {
            $this->roles = $this->serializer->unserialize($this->roles);
        } else {
            $this->roles = null;
        }

        if (!empty($this->permissions)) {
            $this->permissions = $this->serializer->unserialize($this->permissions);
        } else {
            $this->permissions = null;
        }
    }

    public function beforeValidation()
    {
        if (!empty($this->roles)) {
            $this->roles = $this->serializer->serialize($this->roles);
        } else {
            $this->roles = null;
        }

        if (!empty($this->permissions)) {
            $this->permissions = $this->serializer->serialize($this->permissions);
        } else {
            $this->permissions = null;
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Sets a password of a user.
     *
     * @param string $password
     *
     * @return $this
     *
     * @throws \TimurFlush\Auth\Exception\InvalidArgumentException Please see the method
     *                                                             `TimurFlush\Auth\Manager::options()`
     *
     * @throws \TimurFlush\Auth\Exception                          Please see the method
     *                                                             `TimurFlush\Auth\Manager::options()`
     */
    public function setPassword(string $password)
    {
        if (empty($password)) {
            throw new InvalidArgumentException('A password cannot be empty.');
        }

        $this->password = HashingLocator::getDefaultHashing()->hash($password);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \TimurFlush\Auth\Exception                 Please see the method
     *                                                    `TimurFlush\Auth\Hashing\HashingLocator::options()`
     *
     * @throws \TimurFlush\Auth\Exception\UnsafeException If the credentials checking without a password is not allowed.
     */
    public function checkCredentials(array $credentials, bool $allowWithoutPassword = false): bool
    {
        $enteredPassword = null;
        $persistedPassword = null;

        if (isset($credentials['password'])) {
            $needCheckPassword = true;

            $enteredPassword = $credentials['password'];
            $persistedPassword = $this->password ?? null;
        } else {
            /**
             * If a password is not found in user credentials and it's not allowed
             */
            if ($allowWithoutPassword === false) {
                throw new UnsafeException('The credentials checking without a password is not allowed.');
            }

            /**
             * If it's allowed we need to skip the password checking
             */
            $needCheckPassword = false;
        }

        if ($needCheckPassword) {
            /**
             * Close access if any password is not a string
             */
            if (!is_string($enteredPassword) || !is_string($persistedPassword)) {
                return false;
            }

            /**
             * Search a needed hashing
             */
            $hashing = HashingLocator::locate($persistedPassword);

            /**
             * If hashing is not found
             */
            if ($hashing === null) {
                throw new Exception(
                    'The password of the user #' . (int)$this->id . ' does not match any hashing'
                );
            }

            /**
             * Compare hashes
             */
            if (!$hashing->check($enteredPassword, $persistedPassword)) {
                return false;
            }
        }

        /**
         * Compare user credentials
         */
        foreach ($credentials as $credentialName => $credentialValue) {
            if (!property_exists($this, $credentialName)) {
                throw new Exception('The property `' . $credentialName . '` does not exist in ' . static::class);
            }

            if ($this->{$credentialName} !== $credentialValue) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function setBanStatus(?bool $status)
    {
        $this->ban_status = $status;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBanStatus(): bool
    {
        return $this->ban_status ?? false;
    }

    /**
     * {@inheritdoc}
     */
    public function setActivationStatus(?bool $status)
    {
        $this->activation_status = $status;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getActivationStatus(): bool
    {
        return $this->activation_status ?? false;
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     *
     * @throws \TimurFlush\Auth\Exception                          Please see the method `static::addInheritedRole()`
     * @throws \TimurFlush\Auth\Exception\InvalidArgumentException Please see the method `static::addInheritedRole()`
     */
    public function setRoles(array $roles)
    {
        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles(): array
    {
        return $this->roles ?? [];
    }

    /**
     * Removes inherited roles.
     *
     * @return $this
     */
    public function flushRoles()
    {
        $this->roles = null;
        return $this;
    }

    /**
     * Adds a role for current user.
     *
     * @param RoleInterface|string $role
     *
     * @return $this
     *
     * @throws \TimurFlush\Auth\Exception                          If a specified role does not exist.
     * @throws \TimurFlush\Auth\Exception\InvalidArgumentException If a specified role have an invalid type.
     * @throws \TimurFlush\Auth\Exception                          If the 'authManager' service is not registered
     *                                                             in DI.
     */
    public function addRole($role)
    {
        $roleName = null;
        $needSearch = true;

        if ($role instanceof RoleInterface) {
            $roleName = $role->getName();
            $needSearch = false;
        } elseif (is_string($role)) {
            $roleName = $role;
            $needSearch = true;
        } else {
            throw new InvalidArgumentException(
                sprintf(
                    'A role must be the RoleInterface or a string, %s given',
                    is_object($role) ? get_class($role) : gettype($role)
                )
            );
        }

        if (!in_array($roleName, $this->roles)) {
            if ($needSearch) {
                /**
                 * @var Manager $authManager
                 */
                $authManager = $this
                    ->getDI()
                    ->getShared('authManager');

                $findRole = $authManager
                    ->getRoleRepository()
                    ->findByName($role);

                if ($findRole === null) {
                    throw new Exception("A role with the '" . $role . "' name does not exist");
                }
            }

            if (!is_array($this->roles)) {
                $this->roles = [];
            }

            $this->roles[] = $roleName;
        }

        return $this;
    }

    /**
     * Removes an inherited role.
     *
     * @param RoleInterface|string $role
     *
     * @return $this
     *
     * @throws \TimurFlush\Auth\Exception\InvalidArgumentException If a specified role have an invalid type.
     */
    public function removeRole($role)
    {
        $roleName = null;

        if ($role instanceof RoleInterface) {
            $roleName = $role->getName();
        } elseif (is_string($role)) {
            $roleName = $role;
        } else {
            throw new InvalidArgumentException(
                sprintf(
                    'A role must be the RoleInterface or a string, %s given',
                    is_object($role) ? get_class($role) : gettype($role)
                )
            );
        }

        if (is_array($this->roles)) {
            $key = array_search($roleName, $this->roles);

            if (is_scalar($key)) {
                unset($this->roles[$key]);
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function setPermissions(array $permissions)
    {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPermissions(): array
    {
        return $this->permissions ?? [];
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function addPermission(string $permission, bool $value)
    {
        if (!is_array($this->permissions)) {
            $this->permissions = [];
        }

        $this->permissions[$permission] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function rewritePermission(string $permission, bool $newValue, bool $createIfNotExists)
    {
        if (isset($this->permissions[$permission])) {
            $this->permissions[$permission] = $newValue;
        } elseif ($createIfNotExists) {
            $this->addPermission($permission, $newValue);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function removePermission(string $permission)
    {
        if (is_array($this->permissions)) {
            unset($this->permissions[$permission]);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isPermitted(string $permission, callable $callback = null, ...$callbackArguments): bool
    {
        if (isset($this->permissions[$permission])) {
            $effect = $this->permissions[$permission];

            if ($effect === false) {
                return $effect;
            }

            if ($callback === null) {
                return $effect;
            } else {
                return call_user_func($callback, ...$callbackArguments);
            }
        }

        /**
         * @var \TimurFlush\Auth\Role\RepositoryInterface $roleRepository
         */
        $roleRepository = $this
            ->getDI()
            ->getShared('authManager')
            ->getRoleRepository();


        $effect = null;

        foreach ($this->getRoles() as $roleName) {
            if (!is_string($roleName)) {
                throw new Exception(
                    sprintf(
                        'A name of the role must be a string, %s given. User #%s',
                        is_object($roleName) ? get_class($roleName) : gettype($roleName),
                        $this->id
                    )
                );
            }

            $role = $roleRepository->findByName($roleName);

            if ($role === null) {
                continue;
            }

            $effect = $role->isPermitted($permission, $callback, ...$callbackArguments);

            if ($effect === false) {
                continue;
            }

            if ($callback === null) {
                return $effect;
            }

            $effect = call_user_func($callback, ...$callbackArguments);

            if ($effect === true) {
                return $effect;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function flushPermissions()
    {
        $this->permissions = null;
        return $this;
    }

    public function setApiToken(string $token)
    {
        if (mb_strlen($token) < 32) {
            throw new UnsafeException('The API Token must be longer than 32 characters');
        }

        $this->api_token = $token;
    }

    public function getApiToken(): ?string
    {
        return $this->api_token;
    }
}
