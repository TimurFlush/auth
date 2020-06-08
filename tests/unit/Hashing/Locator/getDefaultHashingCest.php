<?php

namespace TimurFlush\Auth\Tests\Unit\Hashing\Locator;

use TimurFlush\Auth\Hashing\BCrypt;
use UnitTester;
use TimurFlush\Auth\Hashing\HashingLocator;

class getDefaultHashingCest
{
    public function check(UnitTester $I)
    {
        $I->wantToTest('HashingLocator::getDefaultHashing()');

        $I->assertInstanceOf(
            BCrypt::class,
            HashingLocator::getDefaultHashing(),
        );
    }
}
