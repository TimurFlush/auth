<?php

declare(strict_types=1);

namespace TimurFlush\Auth;

use TimurFlush\Auth\Activation\ActivationInterface;
use TimurFlush\Auth\User\UserInterface;

interface ManagerInterface
{
    /**
     * Registers a new user.
     *
     * @param array         $credentials
     * @param \Closure|bool $activate
     *
     * @return UserInterface
     */
    public function register(array $credentials, $activate = false): UserInterface;

    /**
     * @param int    $userId
     * @param string $activationId
     *
     * @return bool
     */
    public function attemptActivate(int $userId, string $activationId): bool;

    /**
     * @param UserInterface $user
     *
     * @return ActivationInterface
     */
    public function createActivation(UserInterface $user): ActivationInterface;

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
    public function activateByUser(UserInterface $user): bool;

    public function setSqlDateFormat(string $format);

    public function getSqlDateFormat(): string;
}
