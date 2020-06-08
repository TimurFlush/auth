<?php

namespace TimurFlush\Auth\Tests\Unit\User\UserModel;

use TimurFlush\Auth\Exception\UnsafeException;
use TimurFlush\Auth\User\UserModel;
use UnitTester;

class CheckCredentialsCest
{
    public function checkCredentials(UnitTester $I)
    {
        $I->wantToTest('UserModel::checkCredentials()');

        $userModel = new UserModel();
        $userModel->setId($id = random_int(1, 99));
        $userModel->setPassword($rawPassword = random_bytes(32));

        /*
         * Case 1: Credentials checking without a password is not allowed
         */
        $I->expectThrowable(new UnsafeException('The credentials checking without a password is not allowed.'), function () use ($userModel, $id) {
            $userModel->checkCredentials(['id' => $id]);
        });

        /*
         * Case 2: With invalid credentials
         */
        $I->assertFalse(
            $userModel->checkCredentials(['id' => 101], true)
        );

        /*
         * Case 3: With valid credentials and without a password
         */
        $I->assertTrue(
            $userModel->checkCredentials(['id' => $id], true)
        );

        /*
         * Case 4: With incorrect password
         */
        $I->assertFalse(
            $userModel->checkCredentials(['id' => $id, 'password' => false])
        );

        /*
         * Case 5: With valid credentials
         */
        $I->assertTrue(
            $userModel->checkCredentials(['id' => $id, 'password' => $rawPassword])
        );

        /*
         * Case 6: With invalid password
         */
        $I->assertFalse(
            $userModel->checkCredentials(['id' => $id, 'password' => 'invalidPassword'])
        );
    }
}
