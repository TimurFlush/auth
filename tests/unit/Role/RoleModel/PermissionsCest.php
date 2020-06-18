<?php

namespace TimurFlush\Auth\Tests\Unit\Role\RoleModel;

use TimurFlush\Auth\Role\RoleInterface;
use TimurFlush\Auth\Role\RepositoryInterface as RoleRepositoryInterface;
use TimurFlush\Auth\Tests\Support\Auth\Role\RoleModel;
use UnitTester;
use Mockery as m;

class PermissionsCest
{
    public function getPermissions(UnitTester $I)
    {
        $I->wantToTest('RoleModel::getPermissions()');

        $roleModel = new RoleModel();

        $I->assertEquals(
            [],
            $roleModel->getPermissions()
        );
    }

    public function setPermissions(UnitTester $I)
    {
        $I->wantToTest('RoleModel::setPermissions()');

        $roleModel = new RoleModel();
        $roleModel->setPermissions($exp = ['firstPerm', 'secondPerm']);

        $I->assertEquals(
            $exp,
            $roleModel->getPermissions()
        );
    }

    public function addPermission(UnitTester $I)
    {
        $I->wantToTest('RoleModel::addPermission()');

        $roleModel = new RoleModel();

        $roleModel->addPermission($perm1Name = 'logo.view');
        $roleModel->addPermission($perm2Name = 'dashboard.view');

        $permissions = $roleModel->getPermissions();

        $I->assertContains($perm1Name, $permissions);
        $I->assertContains($perm2Name, $permissions);
    }

    public function removePermission(UnitTester $I)
    {
        $I->wantToTest('RoleModel::removePermission()');

        $roleModel = new RoleModel();

        $roleModel->addPermission($name = 'somePerm');

        $roleModel->removePermission($name);

        $I->assertEmpty($roleModel->getPermissions());
    }

    public function flushPermissions(UnitTester $I)
    {
        $I->wantToTest('RoleModel::flushPermissions()');

        $roleModel = new RoleModel();

        $roleModel->addPermission('somePerm');

        $roleModel->flushPermissions();

        $I->assertEmpty($roleModel->getPermissions());
    }

    public function isPermitted(UnitTester $I)
    {
        $I->wantToTest('RoleModel::isPermitted()');

        $roleModel = new RoleModel();

        /*
         * Case 1: Non-existing permission
         */
        $I->assertFalse($roleModel->isPermitted('non-existing-permission'));

        /*
         * Case 2: Existing permission (true)
         */
        $roleModel->addPermission($name = 'existing-permission');
        $I->assertTrue($roleModel->isPermitted($name));

        /*
         * Case 3: Existing permission (complex)
         */
        $I->assertTrue($roleModel->isPermitted($name, function($a, $b) { return $a + $b === 3; },1, 2));
        $I->assertFalse($roleModel->isPermitted($name, function($a, $b) { return $a + $b === 3; },1, 3));
    }
}
