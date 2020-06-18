<?php

namespace TimurFlush\Auth\Tests\unit\Session\SessionModel;

use Carbon\Carbon;
use TimurFlush\Auth\Tests\Support\Auth\Session\SessionModel;
use UnitTester;

class RevokeCest
{
    public function setUp()
    {
        $authManager = replaceAuthManager();

        /*
         * we need to restore the sql date format
         */
        $authManager->setSqlDateFormat('Y-m-d H:i:s.uO');
    }

    public function isRevoked(UnitTester $I)
    {
        $I->wantToTest('SessionModel::isRevoked()');

        $sessionModel = new SessionModel();

        $I->assertTrue(
            $sessionModel->isRevoked()
        );

        $sessionModel->setExpiresAt(Carbon::now()->addMinutes(5));

        $I->assertFalse(
            $sessionModel->isRevoked()
        );

        $sessionModel->setExpiresAt(Carbon::now()->subMinutes(5));

        $I->assertTrue(
            $sessionModel->isRevoked()
        );
    }

    public function revoke(UnitTester $I)
    {
        $I->wantToTest('SessionModel::revoke()');

        $sessionModel = new SessionModel();
        $sessionModel->setExpiresAt(Carbon::now()->addMinutes(5));

        $I->assertFalse(
            $sessionModel->isRevoked()
        );

        $sessionModel->revoke();

        $I->assertTrue(
            $sessionModel->isRevoked()
        );
    }
}
