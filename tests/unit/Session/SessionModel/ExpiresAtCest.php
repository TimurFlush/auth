<?php

namespace TimurFlush\Auth\Tests\unit\Session\SessionModel;

use Carbon\Carbon;
use TimurFlush\Auth\Tests\Support\Auth\Session\SessionModel;
use UnitTester;

class ExpiresAtCest
{
    public function getExpiresAt(UnitTester $I)
    {
        $I->wantToTest('SessionModel::getExpiresAt()');

        $sessionModel = new SessionModel();

        $I->assertNull(
            $sessionModel->getExpiresAt()
        );
    }

    public function setExpiresAt(UnitTester $I)
    {
        $I->wantToTest('SessionModel::setExpiresAt()');

        $authManager = replaceAuthManager();
        $authManager->setSqlDateFormat($format = 'G j n');

        $G = random_int(0, 23);
        $j = random_int(1, 30);
        $n = random_int(1, 12);

        $time = join(' ', [
            $G, $j, $n
        ]);

        $carbon = Carbon::createFromFormat($format, $time);

        $sessionModel = new SessionModel();
        $sessionModel->setExpiresAt($carbon);

        $carbonExpiresAt = $sessionModel->getExpiresAt();

        $I->assertNotNull($carbonExpiresAt);
        $I->assertInstanceOf(Carbon::class, $carbonExpiresAt);

        $I->assertEquals(
            $time,
            $carbonExpiresAt->format($format)
        );
    }
}
