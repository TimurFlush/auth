<?php

declare(strict_types=1);

namespace TimurFlush\Auth\User\Phalcon;

use Phalcon\Mvc\Model;
use TimurFlush\Auth\Exception;
use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Hashing\HashingInterface;
use TimurFlush\Auth\Hashing\HashingLocator;
use TimurFlush\Auth\Manager;
use TimurFlush\Auth\Permission\PermissionsHolderInterface;
use TimurFlush\Auth\Policy\PolicyExecutorTrait;
use TimurFlush\Auth\Role\RoleInterface;
use TimurFlush\Auth\Support\Model\InteractsWithCreatedAt;
use TimurFlush\Auth\Support\Model\InteractsWithPermissions;
use TimurFlush\Auth\Support\Model\InteractsWithUpdatedAt;
use TimurFlush\Auth\User\UserInterface;
use TimurFlush\Auth\Exception\UnsafeException;

abstract class User extends Model implements UserInterface, PermissionsHolderInterface
{
    use InteractsWithCreatedAt;
    use InteractsWithUpdatedAt;
    use PolicyExecutorTrait;
    use InteractsWithPermissions;

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
     * @Column(type='integer', nullable=true)
     */
    protected ?int $role_id = null;

    /**
     * @Column(type='bool', nullable=true)
     */
    protected ?bool $ban_status = null;

    /**
     * @Column(type='bool', nullable=true)
     */
    protected ?bool $activation_status = null;

    /**
     * Initialize method.
     */
    public function initialize(): void
    {
        $this->setSource('users');
        $this->useDynamicUpdate(true);

        $this->applyCreatedAtBehavior();
        $this->applyUpdatedAtBehavior();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     *
     * @throws \TimurFlush\Auth\Exception\InvalidArgumentException   If an identity is zero and it's forbidden.
     * @throws \TimurFlush\Auth\Exception\InvalidArgumentException   If an identity is negative and it's forbidden.
     *
     * @throws \TimurFlush\Auth\Exception\InvalidArgumentException   Please see the method
     *                                                               `TimurFlush\Auth\Manager::options()`
     *
     * @throws \TimurFlush\Auth\Exception                            Please see the method
     *                                                               `TimurFlush\Auth\Manager::options()`
     */
    public function setId(int $id)
    {
        if ($id === 0 && Manager::options('userModel.allowZeroId')) {
            throw new InvalidArgumentException('An identity cannot be zero.');
        } elseif ($id < 0 && Manager::options('allowNegativeId')) {
            throw new InvalidArgumentException('An identity cannot be negative.');
        }

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

        $this->password = Manager::options('hashing')->hash($password);
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
     * Sets a role id.
     *
     * @param int|RoleInterface $role
     *
     * @return $this
     *
     * @throws \TimurFlush\Auth\Exception\InvalidArgumentException If a role is not an integer
     *                                                             or the \TimurFlush\Auth\RoleModel\RoleInterface
     */
    public function setRoleId($role)
    {
        if ($role instanceof RoleInterface) {
            $this->id = $role->getId();
        } elseif (is_int($role)) {
            $this->id = $role;
        } else {
            throw new InvalidArgumentException(
                sprintf(
                    'A role must be an integer or the %s, %s given',
                    RoleInterface::class,
                    is_object($role) ? get_class($role) : gettype($role)
                )
            );
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoleId(): ?int
    {
        return $this->role_id;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \TimurFlush\Auth\Exception                 Please see the method `TimurFlush\Auth\Manager::options()`
     * @throws \TimurFlush\Auth\Exception\UnsafeException If the credentials checking without a password is not allowed.
     */
    public function checkCredentials(array $credentials): bool
    {
        // If there is no password in credentials, we must set it to null
        // to perform the checks below.
        $originalPassword = $credentials['password'] ?? null;

        // If a user does not have a password, we set it as an empty string.
        $hashedPassword = $this->password ?? '';

        // Remove the password from credentials so that it is not checked
        // for consistency in the loop, as the password is checked separately from the loop.
        unset($credentials['password']);

        // If the provided password is null and verification of credentials
        // without a password is not allowed, we must throw the unsafe exception.
        if (
            $originalPassword === null &&
            Manager::options('allowCredentialsCheckingWithoutPassword') === false
        ) {
            throw new UnsafeException('The credentials checking without a password is not allowed.');
        }

        // Here we mean that a programmer has allowed checking credentials
        // without a password.
        if (is_string($originalPassword)) {
            $hashing = HashingLocator::locate($hashedPassword);

            if ($hashing instanceof HashingInterface) {
                if (!$hashing->check($originalPassword, $hashedPassword)) {
                    return false;
                }
            } elseif (!empty($hashedPassword)) {
                throw new Exception(
                    'The password of the user #' . (int)$this->id . ' does not match any hashing'
                );
            }
        }

        foreach ($credentials as $credentialName => $credentialValue) {
            if (!property_exists($this, $credentialName)) {
                throw new Exception('The property `' . $credentialName . '` does not exists in ' . static::class);
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
        return (bool)$this->ban_status;
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
        return (bool)$this->activation_status;
    }
}
