<?php

namespace TimurFlush\Auth\Tests\Unit\Hashing\Utils;

use TimurFlush\Auth\Hashing\Utils;
use UnitTester;

class RandomStringCest
{
    public function random(UnitTester $I)
    {
        $I->wantToTest('Utils::randomString()');

        $string       = Utils::randomString($expectedLength = 30);
        $actualLength = strlen($string);

        $I->assertEquals($expectedLength, $actualLength);

        $string       = Utils::randomString($expectedLength = 31);
        $actualLength = strlen($string);

        $I->assertEquals($expectedLength, $actualLength);
    }
}
