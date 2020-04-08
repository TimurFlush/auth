<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Session;

use DateTimeInterface;
use Carbon\Carbon;

interface SessionInterface
{
    /**
     * Sets an id of a session.
     *
     * @param string|null $id An id of a session.
     */
    public function setId(string $id);

    /**
     * Returns an id of a session.
     *
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * Sets an id of a user.
     *
     * @param int|string $id An id of a user.
     */
    public function setUserId(int $id);

    /**
     * Returns an id of a user.
     *
     * @return int|null
     */
    public function getUserId(): ?int;

    /**
     * Sets a remember token.
     *
     * @param string $token
     */
    public function setRememberToken(string $token);

    /**
     * Returns a remember token.
     *
     * @return string|null
     */
    public function getRememberToken(): ?string;

    /**
     * Sets a creation time.
     *
     * @param DateTimeInterface $dateTime
     */
    public function setCreatedAt(DateTimeInterface $dateTime);

    /**
     * Returns a creation time.
     *
     * @return Carbon|null
     */
    public function getCreatedAt(): ?Carbon;

    /**
     * Sets an update time.
     *
     * @param DateTimeInterface $dateTime
     */
    public function setUpdatedAt(DateTimeInterface $dateTime);

    /**
     * Returns an update time.
     *
     * @return Carbon|null
     */
    public function getUpdatedAt(): ?Carbon;

    /**
     * Sets an expiry time.
     *
     * @param DateTimeInterface $dateTime
     */
    public function setExpiresAt(DateTimeInterface $dateTime);

    /**
     * Returns an expiry time.
     *
     * @return Carbon|null
     */
    public function getExpiresAt(): ?Carbon;

    /**
     * Revokes a session.
     *
     * @return void
     */
    public function revoke(): void;

    /**
     * Determines whether or not the session has been revoked
     *
     * @return bool
     */
    public function isRevoked(): bool;
}
