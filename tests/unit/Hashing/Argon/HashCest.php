<?php

namespace TimurFlush\Auth\Tests\Unit\Hashing\Argon;

use Codeception\Scenario;
use TimurFlush\Auth\Hashing\Argon;
use UnitTester;

class HashCest
{
    public function hash(UnitTester $I, Scenario $scenario)
    {
        $I->wantToTest('Argon::hash()');

        if (!defined('PASSWORD_ARGON2I') || !defined('PASSWORD_ARGON2ID')) {
            $scenario->skip('Your php build does not support the argon algorithm');
            return;
        }

        /*
         * Case 1: Type 2I
         */
        $argon = new Argon(Argon::TYPE_2I);

        $hash = $argon->hash(random_bytes(32));

        $I->assertIsString($hash);
        $I->assertStringStartsWith('TFArgon', $hash);

        /*
         * Case 2: Type 2D
         */
        $argon = new Argon(Argon::TYPE_2D);

        $hash = $argon->hash(random_bytes(32));

        $I->assertIsString($hash);
        $I->assertStringStartsWith('TFArgon', $hash);
    }
}
