<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Checker;

use TimurFlush\Auth\User\UserInterface;

class Credentials implements CheckerInterface
{
    /**
     * @var array
     */
    protected array $checkableCredentials;

    /**
     * Credentials constructor.
     *
     * @param array $checkableCredentials
     */
    public function __construct(array $checkableCredentials)
    {
        $this->checkableCredentials = $checkableCredentials;
    }

    /**
     * {@inheritDoc}
     */
    public function onValidation(UserInterface $user): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function onAuthentication(UserInterface $user): bool
    {
        return $user->checkCredentials($this->checkableCredentials);
    }
}
