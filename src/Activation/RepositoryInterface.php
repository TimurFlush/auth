<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Activation;

use TimurFlush\Auth\User\UserInterface;

interface RepositoryInterface
{
    public function createNewActivation(UserInterface $user): ActivationInterface;

    public function find(string $activationId, int $userId): ?ActivationInterface;

    public function save(ActivationInterface $activation): void;

    public function delete(ActivationInterface $activation): void;
}
