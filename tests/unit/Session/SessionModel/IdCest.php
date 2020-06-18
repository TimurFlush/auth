<?php

namespace TimurFlush\Auth\Tests\unit\Session\SessionModel;

use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Tests\Support\Auth\Session\SessionModel;
use UnitTester;

class IdCest
{
    public function getId(UnitTester $I)
    {
        $I->wantToTest('SessionModel::getId()');

        $sessionModel = new SessionModel();

        $I->assertEmpty($sessionModel->getId());
    }

    public function setId(UnitTester $I)
    {
        $I->wantToTest('SessionModel::setId()');

        $sessionModel = new SessionModel();

        /*
         * Case 1: Normal mode
         */
        $sessionModel->setId($randomID = random_bytes(32));

        $I->assertEquals(
            $randomID,
            $sessionModel->getId()
        );

        /*
         * Case 2: Empty ID
         */
        $I->expectThrowable(new InvalidArgumentException('A session id cannot be empty.'), function () use ($sessionModel) {
            $sessionModel->setId('');
        });
    }
}
