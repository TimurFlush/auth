<?php

namespace TimurFlush\Auth\Tests\Unit\Hashing\BCrypt;

use TimurFlush\Auth\Hashing\BCrypt;
use UnitTester;

class Helper
{
    public static function extractCost(BCrypt $BCrypt): int
    {
        $refl         = new \ReflectionObject($BCrypt);
        $costProperty = $refl->getProperty('cost');
        $costProperty->setAccessible(true);
        return $costProperty->getValue($BCrypt);
    }
}
