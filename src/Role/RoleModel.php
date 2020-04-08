<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Role\Phalcon;

use Phalcon\Mvc\Model;
use TimurFlush\Auth\Permission\PermissionsHolderInterface;
use TimurFlush\Auth\Role\RoleInterface;
use TimurFlush\Auth\Support\Phalcon\Model\InteractsWithPermissions;

class Role extends Model implements RoleInterface, PermissionsHolderInterface
{
    use InteractsWithPermissions;

    /**
     * @Column(type='integer', nullable=false)
     * @Primary
     * @Identity
     */
    public ?int $id;

    /**
     * @Column(type='varchar', nullable=false)
     */
    public ?string $name;

    public function initialize()
    {
        $this->setSource('roles');
        $this->useDynamicUpdate(true);
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
}
