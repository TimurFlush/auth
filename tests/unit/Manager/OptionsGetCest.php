<?php

namespace TimurFlush\Auth\Tests\Unit\Manager;

use TimurFlush\Auth\Exception;
use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Manager;
use UnitTester;

class OptionsGetCest
{
    public function _before(UnitTester $I)
    {
        Manager::initialize();
    }

    public function _after(UnitTester $I)
    {
        Manager::reset();
    }

    public function optionsGetByNameWithNonExistingOption(UnitTester $I)
    {
        $I->wantToTest('Manager::options() | Get option by name with non-existing option');

        $optionName = 'NonExistingOption';
        $expectedException = new InvalidArgumentException("The `" . $optionName . "` does not exist.");

        $I->expectThrowable(
            $expectedException,
            function () use ($optionName) {
                Manager::options($optionName);
            });
    }
}
