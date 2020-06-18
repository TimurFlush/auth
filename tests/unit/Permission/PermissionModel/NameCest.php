<?php

namespace TimurFlush\Auth\Tests\unit\Permission\PermissionModel;

use TimurFlush\Auth\Tests\Support\Auth\Permission\PermissionModel;
use UnitTester;

class NameCest
{
    public function getName(UnitTester $I)
    {
        $I->wantToTest('PermissionModel::getName()');

        $sessionModel = new PermissionModel();

        $I->assertEmpty($sessionModel->getName());
    }

    public function setName(UnitTester $I)
    {
        $I->wantToTest('PermissionModel::setName()');

        $sessionModel = new PermissionModel();

        /*
         * Case 1: Normal mode
         */
        $sessionModel->setName($randomName = random_bytes(32));

        $I->assertEquals(
            $randomName,
            $sessionModel->getName()
        );
    }
}
