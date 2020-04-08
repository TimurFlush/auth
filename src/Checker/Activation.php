<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Checker;

use TimurFlush\Auth\User\UserInterface;

class Activation implements OptionalCheckerInterface
{
    public function onValidation(UserInterface $user): bool
    {
        return $user->getActivationStatus() === true;
    }

    public function onAuthentication(UserInterface $user): bool
    {
        return $user->getActivationStatus() === true;
    }
}
