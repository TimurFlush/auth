<?php

namespace TimurFlush\Auth\Tests\Unit\Hashing\BCrypt;

use Codeception\Scenario;
use TimurFlush\Auth\Hashing\Argon;
use UnitTester;

class OptionsCest
{
    public function defaultOptions(UnitTester $I, Scenario $scenario)
    {
        $I->wantToTest('Argon::__construct() | Default options');

        if (!defined('PASSWORD_ARGON2I') || !defined('PASSWORD_ARGON2ID')) {
            $scenario->skip('Your php build does not support the argon algorithm');
            return;
        }

        $argon = new Argon(Argon::TYPE_2I);

        $refl = new \ReflectionObject($argon);

        $memoryCost = $refl->getProperty('memoryCost');
        $memoryCost->setAccessible(true);
        $actualMemoryCost = $memoryCost->getValue($argon);

        $timeCost = $refl->getProperty('timeCost');
        $timeCost->setAccessible(true);
        $actualTimeCost = $timeCost->getValue($argon);

        $threads = $refl->getProperty('threads');
        $threads->setAccessible(true);
        $actualThreads = $threads->getValue($argon);

        $I->assertEquals(PASSWORD_ARGON2_DEFAULT_MEMORY_COST, $actualMemoryCost);
        $I->assertEquals(PASSWORD_ARGON2_DEFAULT_TIME_COST, $actualTimeCost);
        $I->assertEquals(PASSWORD_ARGON2_DEFAULT_THREADS, $actualThreads);
    }

    public function setting(UnitTester $I, Scenario $scenario)
    {
        $I->wantToTest('Argon::__construct() | Setting options');

        if (!defined('PASSWORD_ARGON2I') || !defined('PASSWORD_ARGON2ID')) {
            $scenario->skip('Your php build does not support the argon algorithm');
            return;
        }

        $argon = new Argon(
            $expectedType       = Argon::TYPE_2I,
            $expectedMemoryCost = 1,
            $expectedTimeCost   = 2,
            $expectedThreads    = 3
        );

        $refl = new \ReflectionObject($argon);

        $type = $refl->getProperty('type');
        $type->setAccessible(true);
        $actualType = $type->getValue($argon);

        $memoryCost = $refl->getProperty('memoryCost');
        $memoryCost->setAccessible(true);
        $actualMemoryCost = $memoryCost->getValue($argon);

        $timeCost = $refl->getProperty('timeCost');
        $timeCost->setAccessible(true);
        $actualTimeCost = $timeCost->getValue($argon);

        $threads = $refl->getProperty('threads');
        $threads->setAccessible(true);
        $actualThreads = $threads->getValue($argon);

        $I->assertEquals($expectedType,       $actualType);
        $I->assertEquals($expectedMemoryCost, $actualMemoryCost);
        $I->assertEquals($expectedTimeCost,   $actualTimeCost);
        $I->assertEquals($expectedThreads,    $actualThreads);
    }
}
