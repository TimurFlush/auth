<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Accessor;

use TimurFlush\Auth\Checker\CheckerInterface;
use TimurFlush\Auth\Checker\OptionalCheckerInterface;
use TimurFlush\Auth\Session\SessionInterface;
use TimurFlush\Auth\User\UserInterface;

interface AccessorInterface
{
    /**
     * Determines if a user is authenticated.
     *
     * @return bool
     */
    public function isAuth(): bool;

    /**
     * Determines whether the user is unauthenticated
     *
     * @return bool
     */
    public function isGuest(): bool;

    /**
     * Sets a UserInterface object.
     *
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * Returns a UserInterface object.
     *
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface;

    /**
     * Sets a SessionInterface object.
     *
     * @param SessionInterface|null $session
     */
    public function setSession(?SessionInterface $session);

    /**
     * Returns a SessionInterface object.
     *
     * @return SessionInterface|null
     */
    public function getSession(): ?SessionInterface;

    /**
     * Get a user id.
     *
     * @return int|null
     */
    public function getUserId(): ?int;

    /**
     * Get a session id.
     *
     * @return string|null
     */
    public function getSessionId(): ?string;
}
