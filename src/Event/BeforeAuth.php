<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Event\Object;

use TimurFlush\Auth\Accessor\StatelessAccessorInterface;
use TimurFlush\Auth\Event\EventInterface;
use TimurFlush\Auth\Session\SessionInterface;
use TimurFlush\Auth\User\UserInterface;

class BeforeAuth implements EventInterface
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
     * @var StatelessAccessorInterface
     */
    protected StatelessAccessorInterface $statelessAccessor;

    /**
     * BeforeAuth constructor.
     *
     * @param UserInterface              $user
     * @param StatelessAccessorInterface $statelessAccessor
     */
    public function __construct(
        StatelessAccessorInterface $statelessAccessor,
        UserInterface $user
    ) {
        $this->user = $user;
        $this->statelessAccessor = $statelessAccessor;
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
     *
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * Returns a stateless accessor which associated with this event.
     *
     * @return StatelessAccessorInterface|null
     */
    public function getStatelessAccessor(): ?StatelessAccessorInterface
    {
        return isset($this->statelessAccessor)
            ? $this->statelessAccessor
            : null;
    }
}
