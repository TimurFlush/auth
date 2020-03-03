<?php

namespace TimurFlush\Auth\Tests\Unit\Hashing\Whirlpool;

use TimurFlush\Auth\Hashing\Whirlpool;
use UnitTester;

class CheckCest
{
    public function check(UnitTester $I)
    {
        $I->wantToTest('Whirlpool::check()');

        $password = random_bytes(32);

        $whirlpool = new Whirlpool();

        $passwordHash = $whirlpool->hash($password);

        /**
         * Is same password
         */
        $I->assertTrue($whirlpool->check($password, $passwordHash));

        /**
         * Is not same password
         */
        $I->assertFalse($whirlpool->check('notSamePassword', $passwordHash));
    }
}
