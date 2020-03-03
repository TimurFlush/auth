<?php

namespace TimurFlush\Auth\Tests\Unit\Hashing\BCrypt;

use TimurFlush\Auth\Hashing\BCrypt;
use UnitTester;

class CheckCest
{
    public function check(UnitTester $I)
    {
        $I->wantToTest('BCrypt::check()');

        $password = random_bytes(32);

        $bcrypt = new BCrypt();

        $passwordHash = $bcrypt->hash($password);

        /**
         * Is same password
         */
        $I->assertTrue($bcrypt->check($password, $passwordHash));

        /**
         * Is not same password
         */
        $I->assertFalse($bcrypt->check('notSamePassword', $passwordHash));
    }
}
