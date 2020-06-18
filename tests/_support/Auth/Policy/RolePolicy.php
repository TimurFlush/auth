<?php

namespace TimurFlush\Auth\Tests\Support\Auth\Policy;

use TimurFlush\Auth\Role\RoleInterface;
use TimurFlush\Auth\User\UserInterface;

class RolePolicy
{
    public function checkId(UserInterface $user)
    {
        return $user->getId() === 3;
    }

    public function withExtraArguments(RoleInterface $role, $fArg, $sArg)
    {

    }

    public function CheckingRolePolicyOnUserWithoutRoleViaSimplePolicy(RoleInterface $role)
    {
        return true;
    }

    public function CheckingRolePolicyOnUserWithRoleViaSimplePolicy(RoleInterface $role)
    {
        return $role->getId() === 2;
    }
}
