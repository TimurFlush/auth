<?php

namespace TimurFlush\Auth\Tests\Unit\Hashing\Locator;

use TimurFlush\Auth\Exception;
use TimurFlush\Auth\Hashing\Argon;
use TimurFlush\Auth\Hashing\BCrypt;
use TimurFlush\Auth\Hashing\Whirlpool;
use UnitTester;
use TimurFlush\Auth\Hashing\HashingLocator;

class LocateCest
{
    public function locate(UnitTester $I)
    {
        $I->wantToTest('HashingLocator::locate()');

        $fakeHash = 'fakeHash';

        $I->assertNull(
            HashingLocator::locate($fakeHash),
            'On fake hash'
        );

        $bcryptMockHash = 'TFBCrypt';

        $I->assertInstanceOf(
            BCrypt::class,
            HashingLocator::locate($bcryptMockHash),
            'On bcrypt hash'
        );

        $whirlpoolMockHash = 'TFWhirlpool';

        $I->assertNull(
            HashingLocator::locate($whirlpoolMockHash),
            'On whirlpool hash, but the whirlpool hashing is not registered'
        );

        HashingLocator::register(function () {
            return new Argon(Argon::TYPE_2I);
        });

        HashingLocator::register(function () {
            return new Whirlpool();
        });

        $I->assertInstanceOf(
            Whirlpool::class,
            HashingLocator::locate($whirlpoolMockHash),
            'On whirlpool hash'
        );
    }

    public function locateWithIncorrectHashing(UnitTester $I)
    {
        $I->wantToTest('HashingLocator::locate() | With incorrect hashing');

        HashingLocator::register(function () {
            return 123;
        });

        $I->expectThrowable(new Exception('An element of locator pool must be implement the HashingInterface'), function () {
            HashingLocator::locate('someHash');
        });
    }
}
