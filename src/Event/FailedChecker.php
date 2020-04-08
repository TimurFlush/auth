<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Event;

use TimurFlush\Auth\Checker\CheckerInterface;
use TimurFlush\Auth\Session\SessionInterface;
use TimurFlush\Auth\User\UserInterface;

class FailedChecker implements EventInterface
{
    public const ON_AUTHENTICATION = 1;

    public const ON_VALIDATION = 2;

    protected CheckerInterface $checker;

    protected UserInterface $user;

    protected ?SessionInterface $session;

    protected int $phase;

    public function __construct(
        CheckerInterface $checker,
        UserInterface $user,
        int $phase,
        ?SessionInterface $session = null
    ) {
        $this->checker = $checker;
        $this->user = $user;
        $this->phase = $phase;
        $this->session = $session;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'failedChecker';
    }

    /**
     * Returns a checker which associated with this event.
     */
    public function getChecker(): CheckerInterface
    {
        return $this->checker;
    }

    /**
     * Returns a user which associated with this event.
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * Returns a session which associated with this event.
     */
    public function getSession(): ?SessionInterface
    {
        return isset($this->session)
            ? $this->session
            : null;
    }

    /**
     * Determines if an error has occurred during the user authentication phase.
     */
    public function onAuthentication(): bool
    {
        return $this->phase === static::ON_AUTHENTICATION;
    }

    /**
     * Determines whether an error has occurred during the user validation phase.
     */
    public function onValidation(): bool
    {
        return $this->phase === static::ON_VALIDATION;
    }
}
