<?php

namespace TimurFlush\Auth\Tests\unit\Activation\ActivationModel;

use TimurFlush\Auth\Tests\Support\Auth\Activation\ActivationModel;
use UnitTester;

class IdCest
{
    public function getId(UnitTester $I)
    {
        $I->wantToTest('ActivationModel::getId()');

        $activationModel = new ActivationModel();

        $I->assertEmpty($activationModel->getId());
    }

    public function setId(UnitTester $I)
    {
        $I->wantToTest('ActivationModel::setId()');

        $activationModel = new ActivationModel();

        /*
         * Case 1: Normal mode
         */
        $activationModel->setId($randomID = random_bytes(32));

        $I->assertEquals(
            $randomID,
            $activationModel->getId()
        );
    }
}
