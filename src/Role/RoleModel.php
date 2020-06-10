<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Role;

use Phalcon\Mvc\Model;
use TimurFlush\Auth\Serializer\SerializerAwareInterface;
use TimurFlush\Auth\Serializer\SerializerInterface;

/**
 * @property int    $id
 * @property string $name
 * @property string $description
 * @property array  $permissions
 */
abstract class RoleModel extends Model implements RoleInterface, SerializerAwareInterface
{
    /**
     * @Column(type='integer', nullable=false)
     * @Primary
     * @Identity
     */
    protected ?int $id;

    /**
     * @Column(type='varchar', nullable=false)
     */
    protected ?string $name;

    /**
     * @Column(type='text', nullable=true)
     */
    protected ?string $description;

    /**
     * @Column(type='text', nullable=true)
     * @var string|array|null
     */
    protected $permissions;

    /**
     * @var \TimurFlush\Auth\Serializer\SerializerInterface
     */
    protected SerializerInterface $serializer;

    public function initialize()
    {
        $this->setSource('roles');
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
        if (!empty($this->permissions)) {
            $this->permissions = $this->serializer->unserialize($this->permissions);
        } else {
            $this->permissions = null;
        }
    }

    public function beforeValidation()
    {
        if (!empty($this->permissions)) {
            $this->permissions = $this->serializer->serialize(
                array_values($this->permissions)
            );
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
     * {@inheritDoc}
     *
     * @return $this
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): ?string
    {
        return $this->name;
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
    public function addPermission(string $permission)
    {
        if (!is_array($this->permissions)) {
            $this->permissions = [];
        }

        if (!in_array($permission, $this->permissions)) {
            $this->permissions[] = $permission;
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
            $key = array_search($permission, $this->permissions);

            if (is_scalar($key)) {
                unset($this->permissions[$key]);
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isPermitted(string $permission, callable $callback = null, ...$callbackArguments): bool
    {
        if (!is_array($this->permissions)) {
            return false;
        }

        $has = in_array($permission, $this->permissions);

        if ($has === false) {
            return $has;
        }

        if ($callback === null) {
            return $has;
        }

        return call_user_func($callback, ...$callbackArguments);
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
}
