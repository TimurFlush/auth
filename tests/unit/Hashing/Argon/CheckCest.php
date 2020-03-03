<?php

namespace TimurFlush\Auth\Tests\Unit\Hashing\Argon;

use Codeception\Scenario;
use TimurFlush\Auth\Hashing\Argon;
use UnitTester;

class CheckCest
{
    public function check(UnitTester $I, Scenario $scenario)
    {
        $I->wantToTest('Argon::check()');

        if (!defined('PASSWORD_ARGON2I') || !defined('PASSWORD_ARGON2ID')) {
            $scenario->skip('Your php build does not support the argon algorithm');
            return;
        }

        $argon = new Argon(Argon::TYPE_2I);

        $password = random_bytes(32);

        $passwordHash = $argon->hash($password);

        /**
         * Is same password
         */
        $I->assertTrue($argon->check($password, $passwordHash));

        /**
         * Is not same password
         */
        $I->assertFalse($argon->check('notSamePassword', $passwordHash));
    }
}
