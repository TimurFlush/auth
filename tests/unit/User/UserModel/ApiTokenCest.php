<?php

namespace TimurFlush\Auth\Tests\Unit\User\UserModel;

use TimurFlush\Auth\Exception\UnsafeException;
use TimurFlush\Auth\Tests\Support\Auth\User\UserModel;
use UnitTester;

class ApiTokenCest
{
    public function getId(UnitTester $I)
    {
        $I->wantToTest('UserModel::getApiToken()');

        $userModel = new UserModel();

        $I->assertNull($userModel->getApiToken());
    }

    public function setId(UnitTester $I)
    {
        $I->wantToTest('UserModel::setApiToken()');

        $userModel = new UserModel();

        /*
         * Case 1: Normal mode
         */
        $userModel->setApiToken($exp = random_bytes(64));

        $I->assertEquals(
            $exp,
            $userModel->getApiToken()
        );

        /*
         * Case 2: Short token
         */
        $I->expectThrowable(new UnsafeException("The API Token must be longer than 32 characters or equal"), function () use ($userModel) {
            $userModel->setApiToken('short');
        });
    }
}
