<?php

namespace TimurFlush\Auth\Tests\Unit\User\UserModel;

use TimurFlush\Auth\Tests\Support\Auth\User\UserModel;
use UnitTester;

class BanStatusCest
{
    public function getBanStatus(UnitTester $I)
    {
        $I->wantToTest('UserModel::getBanStatus()');

        $userModel = new UserModel();

        $I->assertFalse($userModel->getBanStatus());
    }

    public function setBanStatus(UnitTester $I)
    {
        $I->wantToTest('UserModel::setBanStatus()');

        $userModel = new UserModel();

        $userModel->setBanStatus($exp = true);

        $I->assertEquals(
            $exp,
            $userModel->getBanStatus()
        );

        $userModel->setBanStatus(null);

        $I->assertFalse($userModel->getBanStatus());
    }
}
