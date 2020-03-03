<?php

namespace TimurFlush\Auth\Tests\Unit\Hashing\BCrypt;

use TimurFlush\Auth\Hashing\BCrypt;
use UnitTester;

class HashCest
{
    public function hash(UnitTester $I)
    {
        $I->wantToTest('BCrypt::hash()');

        $bcrypt = new BCrypt();

        $hash = $bcrypt->hash(random_bytes(32));

        $I->assertIsString($hash);
        $I->assertStringStartsWith('$2y$', $hash);

        $length = strlen($hash);

        $I->assertTrue($length >= 60);
    }
}
