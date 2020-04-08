<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Event\Object;

use TimurFlush\Auth\Activation\ActivationInterface;
use TimurFlush\Auth\Event\EventInterface;
use TimurFlush\Auth\User\UserInterface;

class NeedActivation implements EventInterface
{
    /**
     * @var \TimurFlush\Auth\User\UserInterface
     */
    protected UserInterface $user;

    /**
     * @var \TimurFlush\Auth\Activation\ActivationInterface
     */
    protected ActivationInterface $activation;

    /**
     * AfterRegister constructor.
     *
     * @param UserInterface              $user
     */
    public function __construct(
        UserInterface $user,
        ActivationInterface $activation
    ) {
        $this->user = $user;
        $this->activation = $activation;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'needActivation';
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

    public function getActivation(): ActivationInterface
    {
        return $this->activation;
    }
}
