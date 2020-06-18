<?php

namespace TimurFlush\Auth\Tests\Unit\Role\RoleModel;

use TimurFlush\Auth\Tests\Support\Auth\Role\RoleModel;
use UnitTester;

class IdCest
{
    public function getId(UnitTester $I)
    {
        $I->wantToTest('RoleModel::getId()');

        $roleModel = new RoleModel();

        $I->assertNull($roleModel->getId());
    }

    public function setId(UnitTester $I)
    {
        $I->wantToTest('RoleModel::setId()');

        $roleModel = new RoleModel();

        $roleModel->setId($expId = 228);

        $I->assertEquals(
            $expId,
            $roleModel->getId()
        );
    }
}
