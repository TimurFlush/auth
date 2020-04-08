<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Event\Object;

use TimurFlush\Auth\Event\EventInterface;
use TimurFlush\Auth\User\UserInterface;

class AfterRegister implements EventInterface
{
    /**
     * @var UserInterface
     */
    protected UserInterface $user;

    /**
     * AfterRegister constructor.
     *
     * @param UserInterface              $user
     */
    public function __construct(UserInterface $user) {
        $this->user = $user;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'afterRegister';
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
}
