<?php

namespace TimurFlush\Auth\Tests\Unit\Role\RoleModel;

use TimurFlush\Auth\Tests\Support\Auth\Role\RoleModel;
use UnitTester;

class NameCest
{
    public function getName(UnitTester $I)
    {
        $I->wantToTest('RoleModel::getName()');

        $roleModel = new RoleModel();

        $I->assertNull($roleModel->getName());
    }

    public function setName(UnitTester $I)
    {
        $I->wantToTest('RoleModel::setName()');

        $roleModel = new RoleModel();

        $roleModel->setName($randomName = random_bytes(32));

        $I->assertEquals(
            $randomName,
            $roleModel->getName()
        );
    }
}
