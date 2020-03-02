<?php

namespace TimurFlush\Auth\Tests\Unit\Manager;

use TimurFlush\Auth\Exception;
use TimurFlush\Auth\Manager;
use UnitTester;

class OptionsWithUninitializedManagerCest
{
    public function optionsGetWithUninitializedManager(UnitTester $I)
    {
        $I->wantToTest('Manager::options() | Throwing exception if the manager is not initialized');

        $expectedException = new Exception('Before call the static::options() method, you need to call the static::initialize()');

        $I->expectThrowable(
            $expectedException,
            function () {
                Manager::options();
            });
    }
}
