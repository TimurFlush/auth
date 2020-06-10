<?php

namespace TimurFlush\Auth\Tests\Unit\User\UserModel;

use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Tests\Support\Auth\User\UserModel;
use UnitTester;

class PasswordCest
{
    public function getId(UnitTester $I)
    {
        $I->wantToTest('UserModel::getPassword()');

        $userModel = new UserModel();

        $I->assertNull($userModel->getPassword());
    }

    public function setId(UnitTester $I)
    {
        $I->wantToTest('UserModel::setPassword()');

        $userModel = new UserModel();

        $I->expectThrowable(new InvalidArgumentException('A password cannot be empty.'), function () use ($userModel) {
            $userModel->setPassword('');
        });

        $userModel->setPassword('somePassword');

        $I->assertStringStartsWith('TFBCrypt', $userModel->getPassword());
    }
}
