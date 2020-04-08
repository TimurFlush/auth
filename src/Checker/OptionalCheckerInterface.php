<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Checker;

use TimurFlush\Auth\User\UserInterface;

interface OptionalChecker extends CheckerInterface
{
    public function onAuthentication(UserInterface $user): bool;

    public function onAuthenticated(UserInterface $user): bool;
}
