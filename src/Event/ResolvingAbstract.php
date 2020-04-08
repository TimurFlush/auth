<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Event;

use TimurFlush\Auth\Accessor\StatefulAccessorInterface;
use TimurFlush\Auth\Session\SessionInterface;
use TimurFlush\Auth\User\UserInterface;

abstract class ResolvingAbstract implements EventInterface
{
    protected UserInterface $user;

    protected SessionInterface $session;

    protected StatefulAccessorInterface $statefulAccessor;

    protected int $resolvingType;

    public function __construct(
        UserInterface $user,
        SessionInterface $session,
        StatefulAccessorInterface $statefulAccessor,
        int $resolvingType
    ) {
        $this->user = $user;
        $this->session = $session;
        $this->statefulAccessor = $statefulAccessor;
        $this->resolvingType = $resolvingType;
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
     * Returns a stateful accessor which associated with this event.
     */
    public function getStatefulAccessor(): ?StatefulAccessorInterface
    {
        return isset($this->statefulAccessor)
            ? $this->statefulAccessor
            : null;
    }

    /**
     * Returns a resolving type.
     */
    public function getResolvingType(): int
    {
        return $this->resolvingType;
    }
}
