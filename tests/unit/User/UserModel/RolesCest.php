<?php

namespace TimurFlush\Auth\Tests\Unit\User\UserModel;

use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Exception;
use TimurFlush\Auth\Role\RoleInterface;
use TimurFlush\Auth\Role\RepositoryInterface as RoleRepositoryInterface;
use TimurFlush\Auth\Tests\Support\Auth\User\UserModel;
use UnitTester;
use Mockery as m;

class RolesCest
{
    public function getRoles(UnitTester $I)
    {
        $I->wantToTest('UserModel::getRoles()');

        $userModel = new UserModel();

        $I->assertEquals(
            [],
            $userModel->getRoles()
        );
    }

    public function addRole(UnitTester $I)
    {
        $I->wantToTest('UserModel::addRole()');

        $userModel = new UserModel();

        /*
         * Case 1: The search is not need
         */
        $roleMock = m::mock(RoleInterface::class);
        $roleMock
            ->shouldReceive('getName')
            ->andReturn($exp = 'someNameCase1');

        $userModel->addRole($roleMock);

        $I->assertContains(
            $exp,
            $userModel->getRoles()
        );

        /*
         * Case 2: The search is need
         */
        $roleMock = m::mock(RoleInterface::class);
        $roleMock
            ->shouldReceive('getName')
            ->andReturn($exp = 'someNameCase2');

        $roleRepositoryMock = m::mock(RoleRepositoryInterface::class);
        $roleRepositoryMock
            ->shouldReceive('findByName')
            ->andReturn($roleMock);

        replaceAuthManager(
            null,
            null,
            $roleRepositoryMock
        );

        $userModel->addRole($exp);

        $I->assertContains(
            $exp,
            $userModel->getRoles()
        );

        /*
         * Case 3: A specified role does not exists
         */
        $roleRepositoryMock = m::mock(RoleRepositoryInterface::class);
        $roleRepositoryMock
            ->shouldReceive('findByName')
            ->andReturn(null);

        replaceAuthManager(
            null,
            null,
            $roleRepositoryMock
        );

        $I->expectThrowable(new Exception("A role with the 'non-existing-role' name does not exist"), function () use ($userModel) {
            $userModel->addRole('non-existing-role');
        });

        /*
         * Case 4: Invalid argument
         */
        $I->expectThrowable(new InvalidArgumentException("A role must be the RoleInterface or a string, boolean given"), function () use ($userModel) {
            $userModel->addRole(false);
        });
    }

    public function flushRoles(UnitTester $I)
    {
        $I->wantToTest('UserModel::flushRoles()');

        $userModel = new UserModel();

        $roleMock = m::mock(RoleInterface::class);
        $roleMock
            ->shouldReceive('getName')
            ->andReturn($exp = 'someNameCase1');

        $userModel->addRole($roleMock);

        $I->assertNotEmpty($userModel->getRoles());

        $userModel->flushRoles();

        $I->assertEmpty($userModel->getRoles());
    }

    public function addRoles(UnitTester $I)
    {
        $I->wantToTest('UserModel::addRoles()');

        $userModel = new UserModel();

        $roleMock = m::mock(RoleInterface::class);
        $roleMock
            ->shouldReceive('getName')
            ->andReturn($exp = 'someNameCase1');

        $userModel->addRoles([$roleMock]);

        $I->assertContains(
            $exp,
            $userModel->getRoles()
        );
    }

    public function removeRole(UnitTester $I)
    {
        $I->wantToTest('UserModel::removeRole()');

        $roleMock1 = m::mock(RoleInterface::class);
        $roleMock1
            ->shouldReceive('getName')
            ->andReturn($needle1 = 'someNameCase1');

        $roleMock2 = m::mock(RoleInterface::class);
        $roleMock2
            ->shouldReceive('getName')
            ->andReturn($needle2 = 'someNameCase2');

        $userModel = new UserModel();
        $userModel->addRoles([$roleMock1, $roleMock2]);

        /*
         * Case 1: Via string
         */
        $userModel->removeRole($needle1);

        $I->assertNotContains(
            $needle1,
            $userModel->getRoles()
        );

        /*
         * Case 2: Via object
         */
        $userModel->removeRole($roleMock2);

        $I->assertNotContains(
            $needle2,
            $userModel->getRoles()
        );

        /*
         * Case 3: Invalid argument
         */
        $I->expectThrowable(new InvalidArgumentException("A role must be the RoleInterface or a string, boolean given"), function () use ($userModel) {
            $userModel->removeRole(false);
        });
    }

    public function hasRole(UnitTester $I)
    {
        $I->wantToTest('UserModel::hasRole()');

        $userModel = new UserModel();

        /*
         * Case 1: Non-existing role
         */
        $I->assertFalse($userModel->hasRole('non-existing-role'));

        /*
         * Case 2: Via string
         */
        $roleMock = m::mock(RoleInterface::class);
        $roleMock
            ->shouldReceive('getName')
            ->andReturn($case2 = 'someNameCase2');

        $userModel->addRole($roleMock);

        $I->assertTrue($userModel->hasRole($case2));

        /*
         * Case 3: Via object
         */
        $I->assertTrue($userModel->hasRole($roleMock));

        /*
         * Case 4: Invalid argument
         */
        $I->expectThrowable(new InvalidArgumentException("A role must be the RoleInterface or a string, boolean given"), function () use ($userModel) {
            $userModel->hasRole(false);
        });
    }
}
