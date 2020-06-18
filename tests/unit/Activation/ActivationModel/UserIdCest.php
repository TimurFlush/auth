<?php

namespace TimurFlush\Auth\Tests\unit\Activation\ActivationModel;

use TimurFlush\Auth\Tests\Support\Auth\Activation\ActivationModel;
use UnitTester;

class UserIdCest
{
    public function getUserId(UnitTester $I)
    {
        $I->wantToTest('ActivationModel::getUserId()');

        $activationModel = new ActivationModel();

        $I->assertEmpty($activationModel->getUserId());
    }

    public function setUserId(UnitTester $I)
    {
        $I->wantToTest('ActivationModel::setUserId()');

        $activationModel = new ActivationModel();

        /*
         * Case 1: Normal mode
         */
        $activationModel->setUserId($randomID = random_int(1, 99));

        $I->assertEquals(
            $randomID,
            $activationModel->getUserId()
        );
    }
}
