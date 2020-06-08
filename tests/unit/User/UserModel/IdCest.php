<?php

namespace TimurFlush\Auth\Tests\Unit\User\UserModel;

use TimurFlush\Auth\User\UserModel;
use UnitTester;

class IdCest
{
    public function getId(UnitTester $I)
    {
        $I->wantToTest('UserModel::getId()');

        $userModel = new UserModel();

        $I->assertNull($userModel->getId());
    }

    public function setId(UnitTester $I)
    {
        $I->wantToTest('UserModel::setId()');

        $userModel = new UserModel();

        $userModel->setId($expId = 228);

        $I->assertEquals(
            $expId,
            $userModel->getId()
        );
    }
}
