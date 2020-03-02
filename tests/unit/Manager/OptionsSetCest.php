<?php

namespace TimurFlush\Auth\Tests\Unit\Manager;

use Codeception\Example;
use TimurFlush\Auth\Exception;
use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Manager;
use UnitTester;

class OptionsSetCest
{
    public function _before(UnitTester $I)
    {
        Manager::initialize();
    }

    public function _after(UnitTester $I)
    {
        Manager::reset();
    }

    public function optionsSetByArrayWithNonExistingOption(UnitTester $I)
    {
        $I->wantToTest('Manager::options() | Set options by array with non-existing option');

        $optionName = 'nonExistingOption';
        $expectedException = new InvalidArgumentException("The `" . $optionName . "` does not exist.");

        $I->expectThrowable(
            $expectedException,
            function () use ($optionName) {
                Manager::options(
                    [
                        $optionName => 'someValue'
                    ]
                );
            });
    }
}
