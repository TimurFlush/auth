<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Event\Object;

use TimurFlush\Auth\Accessor\StatefulAccessorInterface;
use TimurFlush\Auth\Event\EventInterface;
use TimurFlush\Auth\Session\SessionInterface;
use TimurFlush\Auth\User\UserInterface;

class Logout implements EventInterface
{
    /**
     * @var StatefulAccessorInterface
     */
    protected StatefulAccessorInterface $statefulAccessor;

    /**
     * @var UserInterface
     */
    protected UserInterface $user;

    /**
     * @var SessionInterface|null
     */
    protected ?SessionInterface $session;

    /**
     * Logout constructor.
     *
     * @param StatefulAccessorInterface $statefulAccessor
     * @param UserInterface             $user
     * @param SessionInterface|null     $session
     */
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
     *
     * @return StatefulAccessorInterface
     */
    public function getStatefulAccessor(): StatefulAccessorInterface
    {
        return $this->statefulAccessor;
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
     * @return SessionInterface|null
     */
    public function getSession(): ?SessionInterface
    {
        return isset($this->session)
            ? $this->session
            : null;
    }
}
