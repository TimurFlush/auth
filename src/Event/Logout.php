<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Event;

use TimurFlush\Auth\Accessor\StatefulAccessorInterface;
use TimurFlush\Auth\Session\SessionInterface;
use TimurFlush\Auth\User\UserInterface;

class Logout implements EventInterface
{
    protected StatefulAccessorInterface $statefulAccessor;

    protected UserInterface $user;

    protected ?SessionInterface $session;

    public function __construct(
        StatefulAccessorInterface $statefulAccessor,
        UserInterface $user,
        ?SessionInterface $session = null
    ) {
        $this->statefulAccessor = $statefulAccessor;
        $this->user = $user;
        $this->session = $session;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'logout';
    }

    /**
     * Returns a stateful accessor which associated with this event.
     */
    public function getStatefulAccessor(): StatefulAccessorInterface
    {
        return $this->statefulAccessor;
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
}
