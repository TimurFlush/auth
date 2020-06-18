<?php

namespace TimurFlush\Auth\Tests\Unit\Serializer\JsonSerializer;

use TimurFlush\Auth\Exception\SerializerException;
use TimurFlush\Auth\Serializer\JsonSerializer;
use UnitTester;

class SerializeCest
{
    public function serialize(UnitTester $I)
    {
        $I->wantToTest('JsonSerializer::serialize()');

        $jsonSerializer = new JsonSerializer();

        $actual = $jsonSerializer->serialize($data = [
            random_int(1,9),
            random_int(1,9),
            random_int(1, 9)
        ]);

        $I->assertEquals(
            json_encode($data),
            $actual
        );
    }

    public function unserialize(UnitTester $I)
    {
        $I->wantToTest('JsonSerializer::unserialize()');

        $jsonSerializer = new JsonSerializer();

        /*
         * Case 1: Invalid syntax
         */
        $I->expectThrowable(new SerializerException('Syntax error'), function () use ($jsonSerializer) {
            $jsonSerializer->unserialize('-.BrokenData');
        });

        /*
         * Case 2: Normal mode
         */
        $actual = $jsonSerializer->unserialize(
            json_encode($originalData = [
                random_int(1, 9),
                random_int(1, 9),
                random_int(1, 9)
            ])
        );

        $I->assertEquals(
            $originalData,
            $actual
        );
    }
}
