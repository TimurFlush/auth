<?php

namespace TimurFlush\Auth\Tests\Support\Auth\Policy;

use TimurFlush\Auth\Role\RoleInterface;
use TimurFlush\Auth\User\UserInterface;

class UserPolicy
{
    public function checkId(UserInterface $userExecutor, UserInterface $userExecuted)
    {
        return $userExecutor->getId() === 2;
    }

    public function withExtraArguments(UserInterface $userExecutor, UserInterface $userExecuted, $fArg = null, $sArg = null)
    {
        return $userExecuted->getId() === 3 && $fArg === 1 && $sArg === 2;
    }

    public function doesNotAllowForGuests(UserInterface $user)
    {
        // N O P
    }

    public function checkRole(RoleInterface $role)
    {
        return true;
    }

    public function checkRoleOnAdmin(RoleInterface $role)
    {
        return $role->getName() === 'admin';
    }
}
