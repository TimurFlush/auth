<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Activation;

use Phalcon\Mvc\Model;

abstract class ActivationModel extends Model implements ActivationInterface
{
    /**
     * @Column(type='varchar', nullable=false)
     * @Primary
     */
    protected ?string $id = null;

    /**
     * @Column(type='biginteger', nullable=false)
     * @Primary
     */
    protected ?int $user_id = null;

    public function initialize()
    {
        $this->setSource('activations');
        $this->useDynamicUpdate(true);
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function setId(string $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function setUserId(int $userId)
    {
        $this->user_id = $userId;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getUserId(): ?int
    {
        return $this->user_id;
    }
}
