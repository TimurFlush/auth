<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Event\Object;

use TimurFlush\Auth\Accessor\StatefulAccessorInterface;
use TimurFlush\Auth\Event\EventInterface;
use TimurFlush\Auth\Session\SessionInterface;
use TimurFlush\Auth\User\UserInterface;

abstract class ResolvingAbstract implements EventInterface
{
    /**
     * @var UserInterface
     */
    protected UserInterface $user;

    /**
     * @var SessionInterface
     */
    protected SessionInterface $session;

    /**
     * @var StatefulAccessorInterface
     */
    protected StatefulAccessorInterface $statefulAccessor;

    /**
     * @var int
     */
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
     *
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * Returns a session which associated with this event.
     *
     * @return SessionInterface
     */
    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    /**
     * Returns a stateful accessor which associated with this event.
     *
     * @return StatefulAccessorInterface|null
     */
    public function getStatefulAccessor(): ?StatefulAccessorInterface
    {
        return isset($this->statefulAccessor)
            ? $this->statefulAccessor
            : null;
    }

    /**
     * Returns a resolving type.
     *
     * @return int
     */
    public function getResolvingType(): int
    {
        return $this->resolvingType;
    }
}
