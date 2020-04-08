<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Session\Phalcon;

use Carbon\Carbon;
use Phalcon\Mvc\Model;
use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Manager;
use TimurFlush\Auth\Session\SessionInterface;
use TimurFlush\Auth\Support\Model\InteractsWithCreatedAt;
use TimurFlush\Auth\Support\Model\InteractsWithUpdatedAt;
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
     */
    public function setExpiresAt(DateTimeInterface $dateTime)
    {
        /**
         * @var Manager $authManager
         */
        $authManager = $this
            ->getDI()
            ->getShared('authManager');

        $this->expires_at = $dateTime->format($authManager->getSqlDateFormat());
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiresAt(): ?Carbon
    {
        /**
         * @var Manager $authManager
         */
        $authManager = $this
            ->getDI()
            ->getShared('authManager');

        return isset($this->expires_at)
            ? Carbon::createFromFormat($authManager->getSqlDateFormat(), $this->expires_at)
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
