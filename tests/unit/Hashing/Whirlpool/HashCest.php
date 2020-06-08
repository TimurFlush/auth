<?php

namespace TimurFlush\Auth\Tests\Unit\Hashing\Whirlpool;

use TimurFlush\Auth\Hashing\Whirlpool;
use UnitTester;

class HashCest
{
    public function hash(UnitTester $I)
    {
        $I->wantToTest('Whirlpool::hash()');

        $whirlpool = new Whirlpool();

        $hash = $whirlpool->hash(random_bytes(32));

        $I->assertIsString($hash);
        $I->assertStringStartsWith('TFWhirlpool', $hash);

        $length = strlen($hash);

        $I->assertTrue($length >= 170);
    }
}
