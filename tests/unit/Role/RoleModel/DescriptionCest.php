<?php

namespace TimurFlush\Auth\Tests\Unit\Role\RoleModel;

use TimurFlush\Auth\Tests\Support\Auth\Role\RoleModel;
use UnitTester;

class DescriptionCest
{
    public function getDescription(UnitTester $I)
    {
        $I->wantToTest('RoleModel::getDescription()');

        $roleModel = new RoleModel();

        $I->assertNull($roleModel->getDescription());
    }

    public function setDescription(UnitTester $I)
    {
        $I->wantToTest('RoleModel::setDescription()');

        $roleModel = new RoleModel();

        $roleModel->setDescription($description = '228');

        $I->assertEquals(
            $description,
            $roleModel->getDescription()
        );
    }
}
