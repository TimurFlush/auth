<?php

namespace TimurFlush\Auth\Tests\Unit\User\UserModel;

use TimurFlush\Auth\Tests\Support\Auth\User\UserModel;
use UnitTester;

class ActivationStatusCest
{
    public function getBanStatus(UnitTester $I)
    {
        $I->wantToTest('UserModel::getActivationStatus()');

        $userModel = new UserModel();

        $I->assertFalse($userModel->getActivationStatus());
    }

    public function setBanStatus(UnitTester $I)
    {
        $I->wantToTest('UserModel::setActivationStatus()');

        $userModel = new UserModel();

        $userModel->setActivationStatus($exp = true);

        $I->assertEquals(
            $exp,
            $userModel->getActivationStatus()
        );

        $userModel->setActivationStatus(null);

        $I->assertFalse($userModel->getActivationStatus());
    }
}
