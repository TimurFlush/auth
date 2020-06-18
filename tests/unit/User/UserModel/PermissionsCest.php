<?php

namespace TimurFlush\Auth\Tests\Unit\User\UserModel;

use TimurFlush\Auth\Role\RoleInterface;
use TimurFlush\Auth\Role\RepositoryInterface as RoleRepositoryInterface;
use TimurFlush\Auth\Tests\Support\Auth\Role\RoleModel;
use TimurFlush\Auth\Tests\Support\Auth\User\UserModel;
use UnitTester;
use Mockery as m;

class PermissionsCest
{
    public function getPermissions(UnitTester $I)
    {
        $I->wantToTest('UserModel::getPermissions()');

        $userModel = new UserModel();

        $I->assertEquals(
            [],
            $userModel->getPermissions()
        );
    }

    public function setPermissions(UnitTester $I)
    {
        $I->wantToTest('UserModel::setPermissions()');

        $userModel = new UserModel();
        $userModel->setPermissions($exp = ['firstPerm', 'secondPerm']);

        $I->assertEquals(
            $exp,
            $userModel->getPermissions()
        );
    }

    public function addPermission(UnitTester $I)
    {
        $I->wantToTest('UserModel::addPermission()');

        $userModel = new UserModel();

        $userModel->addPermission($perm1Name = 'logo.view', $perm1Value = true);
        $userModel->addPermission($perm2Name = 'dashboard.view', $perm2Value = false);

        $permissions = $userModel->getPermissions();

        $I->assertArrayHasKey($perm1Name, $permissions);
        $I->assertEquals($perm1Value, $permissions[$perm1Name]);

        $I->assertArrayHasKey($perm2Name, $permissions);
        $I->assertEquals($perm2Value, $permissions[$perm2Name]);
    }

    public function rewritePermissionCest(UnitTester $I)
    {
        $I->wantToTest('UserModel::rewritePermission()');

        $userModel = new UserModel();

        /*
         * Case 1: Checking the created permission if the "createIfNotExists" argument value is false
         */
        $userModel->rewritePermission($key = 'case1', true, false);
        $I->assertArrayNotHasKey($key, $userModel->getPermissions());

        /*
         * Case 2: Checking the created permission if the "createIfNotExists" argument value is true
         */
        $userModel->rewritePermission($key = 'case2', $value = true, true);
        $I->assertArrayHasKey($key, $userModel->getPermissions());
        $I->assertEquals($value, $userModel->getPermissions()[$key]);

        /*
         * Case 3: Checking for successful rewrite
         */
        $userModel->rewritePermission('case2', $value = false, false);
        $I->assertEquals($value, $userModel->getPermissions()[$key]);
    }

    public function removePermission(UnitTester $I)
    {
        $I->wantToTest('UserModel::removePermission()');

        $userModel = new UserModel();

        $userModel->addPermission($name = 'somePerm', true);

        $userModel->removePermission($name);

        $I->assertEmpty($userModel->getPermissions());
    }

    public function flushPermissions(UnitTester $I)
    {
        $I->wantToTest('UserModel::flushPermissions()');

        $userModel = new UserModel();

        $userModel->addPermission('somePerm', true);

        $userModel->flushPermissions();

        $I->assertEmpty($userModel->getPermissions());
    }

    public function isPermitted(UnitTester $I)
    {
        $I->wantToTest('UserModel::isPermitted()');

        $userModel = new UserModel();

        /*
         * Case 1: Non-existing permission
         */
        $I->assertFalse($userModel->isPermitted('non-existing-permission'));

        /*
         * Case 2: Existing permission (true)
         */
        $userModel->addPermission($name = 'existing-permission', true);
        $I->assertTrue($userModel->isPermitted($name));

        /*
         * Case 3: Existing permission (false)
         */
        $userModel->rewritePermission($name, false, false);
        $I->assertFalse($userModel->isPermitted($name));

        /*
         * Case 4: Existing permission (complex)
         */
        $userModel->rewritePermission($name, true, false);
        $I->assertTrue($userModel->isPermitted($name, function($a, $b) { return $a + $b === 3; },1, 2));
        $I->assertFalse($userModel->isPermitted($name, function($a, $b) { return $a + $b === 3; },1, 3));

        /*
         * Case 5: With roles
         */
        $role51 = new RoleModel();
        $role51->setName($name51 = 'someRole51');
        $role51->addPermission($perm51 = 'somePermission51');

        $role52 = new RoleModel();
        $role52->setName($name52 = 'someRole52');
        $role52->addPermission($perm52 = 'somePermission52');

        $role53Mock = m::mock(RoleInterface::class);
        $role53Mock
            ->shouldReceive('getName')
            ->andReturn($name53 = 'someRole53');

        $userModel->addRoles([$role51, $role52, $role53Mock]);

        $roleRepositoryMock = m::mock(RoleRepositoryInterface::class);
        $roleRepositoryMock
            ->shouldReceive('findByName')
            ->with($name51)
            ->andReturn($role51);
        $roleRepositoryMock
            ->shouldReceive('findByName')
            ->with($name52)
            ->andReturn($role52);
        $roleRepositoryMock
            ->shouldReceive('findByName')
            ->with($name53)
            ->andReturn(null);

        replaceAuthManager(null, null, $roleRepositoryMock);

        $I->assertTrue($userModel->isPermitted($perm51));
        $I->assertTrue($userModel->isPermitted($perm52));

        $I->assertTrue($userModel->isPermitted($perm51, function($a, $b) { return $a + $b === 3; },1, 2));
        $I->assertFalse($userModel->isPermitted($perm52, function($a, $b) { return $a + $b === 3; },1, 3));
    }
}
