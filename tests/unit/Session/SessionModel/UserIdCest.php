<?php

namespace TimurFlush\Auth\Tests\unit\Session\SessionModel;

use TimurFlush\Auth\Tests\Support\Auth\Session\SessionModel;
use UnitTester;

class UserIdCest
{
    public function getUserId(UnitTester $I)
    {
        $I->wantToTest('SessionModel::getUserId()');

        $sessionModel = new SessionModel();

        $I->assertEmpty($sessionModel->getUserId());
    }

    public function setUserId(UnitTester $I)
    {
        $I->wantToTest('SessionModel::setUserId()');

        $sessionModel = new SessionModel();
        $sessionModel->setUserId($userID = random_int(1, 99));

        $I->assertEquals(
            $userID,
            $sessionModel->getUserId()
        );
    }
}
