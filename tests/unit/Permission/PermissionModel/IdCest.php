<?php

namespace TimurFlush\Auth\Tests\unit\Permission\PermissionModel;

use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Tests\Support\Auth\Permission\PermissionModel;
use UnitTester;

class IdCest
{
    public function getId(UnitTester $I)
    {
        $I->wantToTest('PermissionModel::getId()');

        $sessionModel = new PermissionModel();

        $I->assertEmpty($sessionModel->getId());
    }

    public function setId(UnitTester $I)
    {
        $I->wantToTest('PermissionModel::setId()');

        $sessionModel = new PermissionModel();

        /*
         * Case 1: Normal mode
         */
        $sessionModel->setId($randomID = random_int(1,99));

        $I->assertEquals(
            $randomID,
            $sessionModel->getId()
        );
    }
}
