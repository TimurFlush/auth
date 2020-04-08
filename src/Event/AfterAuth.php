<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Event;

use TimurFlush\Auth\Accessor\AccessorInterface;
use TimurFlush\Auth\Session\SessionInterface;
use TimurFlush\Auth\User\UserInterface;

class AfterAuth implements EventInterface
{
    protected UserInterface $user;

    protected SessionInterface $session;

    protected AccessorInterface $statelessAccessor;

    public function __construct(
        AccessorInterface $statelessAccessor,
        UserInterface $user,
        SessionInterface $session
    ) {
        $this->user = $user;
        $this->session = $session;
        $this->statelessAccessor = $statelessAccessor;
    }

    /**
     * Returns the event name.
     */
    public function getName(): string
    {
        return 'afterAuth';
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
    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    /**
     * Returns a stateless accessor which associated with this event.
     */
    public function getStatelessAccessor(): ?AccessorInterface
    {
        return isset($this->statelessAccessor)
            ? $this->statelessAccessor
            : null;
    }
}
