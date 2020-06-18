<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Permission;

use Phalcon\Mvc\Model;

/**
 * @property int         $id
 * @property string      $name
 * @property string|null $description
 */
abstract class PermissionModel extends Model implements PermissionInterface
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
    protected ?string $name = null;

    /**
     * @Column(type='varchar', nullable=true)
     */
    protected ?string $description = null;

    public function initialize()
    {
        $this->setSource('permissions');
        $this->useDynamicUpdate(true);
    }

    /**
     * Sets an id.
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns an id.
     *
     * @return int|null
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
     * Sets a description.
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Returns a description.
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
