<?php

namespace TimurFlush\Auth\Tests\Unit\Hashing\Utils;

use TimurFlush\Auth\Hashing\Utils;
use UnitTester;

class SafelyCompareTest
{
    public function random(UnitTester $I)
    {
        $I->wantToTest('Utils::safelyCompare()');

        $firstString  = 'kfsSoGood';
        $secondString = 'kfcSoGood';

        $I->assertTrue(Utils::safelyCompare($firstString, $secondString));

        $firstString  = 'kfsSoGood';
        $secondString = 'LaGenteEstaMuyLoca';

        $I->assertTrue(Utils::safelyCompare($firstString, $secondString));
    }
}
