<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Event;

use TimurFlush\Auth\Activation\ActivationInterface;
use TimurFlush\Auth\User\UserInterface;

class NeedActivation implements EventInterface
{
    protected UserInterface $user;

    protected ActivationInterface $activation;

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
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * TODO : NEED COMMENTING
     */
    public function getActivation(): ActivationInterface
    {
        return $this->activation;
    }
}
