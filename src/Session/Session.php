<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Session\Phalcon;

use Carbon\Carbon;
use Phalcon\Mvc\Model;
use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Manager;
use TimurFlush\Auth\Session\SessionInterface;
use TimurFlush\Auth\Support\Phalcon\InteractsWithCreatedAt;
use TimurFlush\Auth\Support\Phalcon\InteractsWithUpdatedAt;
use DateTimeInterface;

abstract class Session extends Model implements SessionInterface
{
    use InteractsWithCreatedAt;
    use InteractsWithUpdatedAt;

    /**
     * @Column(type='varchar', nullable=false)
     * @Primary
     * @Identity
     */
    protected ?int $id = null;

    /**
     * @Column(type='biginteger', nullable=false)
     */
    protected ?int $user_id = null;

    /**
     * @Column(type='varchar', nullable=true)
     */
    protected ?string $remember_token;

    /**
     * @Column(type='timestamp', nullable=true)
     */
    protected ?string $expires_at = null;

    /**
     * Initialize method.
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->setSource('sessions');
        $this->useDynamicUpdate(true);

        $this->applyCreatedAtBehavior();
        $this->applyUpdatedAtBehavior();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     *
     * @throws InvalidArgumentException A session id cannot be empty.
     */
    public function setId(string $id)
    {
        if (empty($id)) {
            throw new InvalidArgumentException('A session id cannot be empty.');
        }

        $this->id = $id;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setUserId(int $userId)
    {
        if ($userId === 0 && Manager::options('userModel.allowZeroId')) {
            throw new InvalidArgumentException('An identity cannot be zero.');
        } elseif ($userId < 0 && Manager::options('allowNegativeId')) {
            throw new InvalidArgumentException('An identity cannot be negative.');
        }

        $this->user_id = $userId;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    /**
     * {@inheritdoc}
     */
    public function setRememberToken(string $token)
    {
        $this->remember_token = $token;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRememberToken(): ?string
    {
        return $this->remember_token;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     *
     * @throws InvalidArgumentException   Please see the method `TimurFlush\Auth\Manager::options()`
     * @throws \TimurFlush\Auth\Exception Please see the method `TimurFlush\Auth\Manager::options()`
     */
    public function setExpiresAt(DateTimeInterface $dateTime)
    {
        $this->expires_at = $dateTime->format(Manager::options('date.format'));
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException   Please see the method `TimurFlush\Auth\Manager::options()`
     * @throws \TimurFlush\Auth\Exception Please see the method `TimurFlush\Auth\Manager::options()`
     */
    public function getExpiresAt(): ?Carbon
    {
        return isset($this->expires_at)
            ? Carbon::createFromFormat(Manager::options('date.format'), $this->expires_at)
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function revoke(): void
    {
        $this->expires_at = null;
    }

    /**
     * {@inheritdoc}
     */
    public function isRevoked(): bool
    {
        return $this->expires_at === null;
    }
}
