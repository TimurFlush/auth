<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Policy;

use ReflectionParameter;

final class Belonging
{
    protected bool $toUser = false;

    protected bool $toRole = false;


    public function __construct(ReflectionParameter $reflectedArgument)
    {

    }

    public function toUser(): bool
    {
        return $this->toUser;
    }

    public function toRole(): bool
    {
        return $this->toRole;
    }
}
