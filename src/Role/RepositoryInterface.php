<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Role;

interface RepositoryInterface
{
    public function findByName(string $name): ?RoleInterface;
}
