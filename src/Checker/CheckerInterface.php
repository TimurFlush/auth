<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Checker;

use TimurFlush\Auth\User\UserInterface;

interface CheckerInterface
{
    /**
     * The user authentication phase.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function onAuthentication(UserInterface $user): bool;

    /**
     * The user validation phase.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function onValidation(UserInterface $user): bool;
}
