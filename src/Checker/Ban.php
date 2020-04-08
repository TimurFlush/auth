<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Checker;

use TimurFlush\Auth\User\UserInterface;

class Ban implements OptionalCheckerInterface
{
    public function onValidation(UserInterface $user): bool
    {
        return $user->getBanStatus() === false;
    }

    public function onAuthentication(UserInterface $user): bool
    {
        return $user->getBanStatus() === false;
    }
}
