<?php

namespace TimurFlush\Auth\Tests\Unit\Checker\Credentials;

use TimurFlush\Auth\Checker\Activation;
use TimurFlush\Auth\Checker\Credentials;
use TimurFlush\Auth\User\UserInterface;
use TimurFlush\Auth\User\UserModel;
use UnitTester;
use Mockery as m;

class OnAuthenticationCest
{
    public function onValidation(UnitTester $I)
    {
        $I->wantToTest('Credentials::onAuthentication()');

        $id = random_int(1, 99);
        $password = random_bytes(32);

        $user = new UserModel();
        $user->setId($id);
        $user->setPassword($password);

        /*
         * Case 1: true
         */
        $checker = new Credentials(['id' => $id, 'password' => $password]);
        $I->assertTrue($checker->onAuthentication($user));

        /*
         * Case 2: Invalid password
         */
        $checker = new Credentials(['id' => $id, 'password' => 'invalid-password']);
        $I->assertFalse($checker->onAuthentication($user));

        /*
         * Case 3: Invalid id
         */
        $checker = new Credentials(['id' => 'invalid-id', 'password' => $password]);
        $I->assertFalse($checker->onAuthentication($user));
    }
}
