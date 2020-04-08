<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Activation;

interface ActivationInterface
{
    public function setId(string $id);

    public function getId(): ?string;

    public function setUserId(int $userId);

    public function getUserId(): ?int;
}
