<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Permission;

use Phalcon\Mvc\Model;

class Permission extends Model implements PermissionInterface
{
    /**
     * @Column(type='integer', nullable=false)
     * @Primary
     * @Identity
     */
    protected ?int $id = null;

    /**
     * @Column(type='varchar', nullable=false)
     */
    protected string $name;

    protected string $description;


    public function initialize()
    {

    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
