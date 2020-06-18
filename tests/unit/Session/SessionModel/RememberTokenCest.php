<?php

namespace TimurFlush\Auth\Tests\unit\Session\SessionModel;

use TimurFlush\Auth\Tests\Support\Auth\Session\SessionModel;
use UnitTester;

class RememberTokenCest
{
    public function getRememberToken(UnitTester $I)
    {
        $I->wantToTest('SessionModel::getRememberToken()');

        $sessionModel = new SessionModel();

        $I->assertEmpty($sessionModel->getRememberToken());
    }

    public function setRememberToken(UnitTester $I)
    {
        $I->wantToTest('SessionModel::setRememberToken()');

        $sessionModel = new SessionModel();
        $sessionModel->setRememberToken($rememberToken = random_bytes(32));

        $I->assertEquals(
            $rememberToken,
            $sessionModel->getRememberToken()
        );
    }
}
