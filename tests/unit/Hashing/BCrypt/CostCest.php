<?php

namespace TimurFlush\Auth\Tests\Unit\Hashing\BCrypt;

use TimurFlush\Auth\Hashing\BCrypt;
use UnitTester;

class CostCest
{
    public function defaultCost(UnitTester $I)
    {
        $I->wantToTest('BCrypt::__construct() | Default cost');

        $bcrypt = new BCrypt();

        $actualCost   = Helper::extractCost($bcrypt);
        $expectedCost = 10;

        $I->assertEquals($expectedCost, $actualCost);
    }

    public function settingCost(UnitTester $I)
    {
        $I->wantToTest('BCrypt::__construct() | Setting the cost');

        $bcrypt       = new BCrypt($expectedCost = 9);
        $actualCost   = Helper::extractCost($bcrypt);

        $I->assertEquals($expectedCost, $actualCost);
    }
}
