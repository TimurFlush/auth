<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Role;

interface RoleRepositoryInterface
{
    public function findById(int $id): ?RoleInterface;
}
