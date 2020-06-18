<?php

namespace TimurFlush\Auth\Tests\unit\Permission\PermissionModel;

use TimurFlush\Auth\Tests\Support\Auth\Permission\PermissionModel;
use UnitTester;

class DescriptionCest
{
    public function getDescription(UnitTester $I)
    {
        $I->wantToTest('PermissionModel::getDescription()');

        $sessionModel = new PermissionModel();

        $I->assertEmpty($sessionModel->getDescription());
    }

    public function setDescription(UnitTester $I)
    {
        $I->wantToTest('PermissionModel::setDescription()');

        $sessionModel = new PermissionModel();

        /*
         * Case 1: Normal mode
         */
        $sessionModel->setDescription($randomDescription = random_bytes(32));

        $I->assertEquals(
            $randomDescription,
            $sessionModel->getDescription()
        );
    }
}
