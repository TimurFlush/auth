<?php

namespace TimurFlush\Auth\Tests\Unit\Hashing\Argon;

use Codeception\Scenario;
use TimurFlush\Auth\Hashing\Argon;
use UnitTester;

class ConstantsCest
{
    public function existing(UnitTester $I)
    {
        $I->wantToTest('Argon::class | Check constants existing');

        $refl = new \ReflectionClass(Argon::class);

        $I->assertTrue($refl->hasConstant('TYPE_2I'));
        $I->assertTrue($refl->hasConstant('TYPE_2D'));
    }

    public function defaultValue(UnitTester $I, Scenario $scenario)
    {
        $I->wantToTest('Argon::class | Check a default value of constants');

        $refl   = new \ReflectionClass(Argon::class);

        $actual2I = $refl->getConstant('TYPE_2I');
        $actual2D = $refl->getConstant('TYPE_2D');

        $I->assertEquals('2i', $actual2I);
        $I->assertEquals('2d', $actual2D);
    }
}
