<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Event;

use TimurFlush\Auth\Accessor\AccessorInterface;
use TimurFlush\Auth\Session\SessionInterface;
use TimurFlush\Auth\User\UserInterface;

class BeforeAuth implements EventInterface
{
    protected UserInterface $user;

    protected SessionInterface $session;

    protected AccessorInterface $statelessAccessor;

    protected bool $isOnce;

    public function __construct(
        AccessorInterface $statelessAccessor,
        UserInterface $user,
        bool $isOnce = null
    ) {
        $this->user = $user;
        $this->statelessAccessor = $statelessAccessor;

        if (is_bool($isOnce)) {
            $this->isOnce = $isOnce;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'beforeAuth';
    }

    /**
     * Returns a user which associated with this event.
     */
    public function getUser(): UserInterface
    {
        return $this->user;
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

    /**
     * Determines whether this authentication attempt is temporary.
     */
    public function isOnce(): bool
    {
        return isset($this->isOnce)
            ? $this->isOnce
            : false;
    }
}
