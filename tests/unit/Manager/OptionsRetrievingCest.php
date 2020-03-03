<?php

namespace TimurFlush\Auth\Tests\Unit\Manager;

use Codeception\Example;
use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Hashing\Argon;
use TimurFlush\Auth\Hashing\BCrypt;
use TimurFlush\Auth\Hashing\HashingInterface;
use TimurFlush\Auth\Manager;
use UnitTester;
use Codeception\Stub;

class OptionsRetrievingCest
{
    public function _before(UnitTester $I)
    {
        Manager::initialize();
    }

    public function _after(UnitTester $I)
    {
        Manager::reset();
    }

    public function optionsProvider()
    {
        return [
            [
                'name'  => 'date.format',
                'value' => 'someFormat1',
            ],
            [
                'name'   => 'date.format',
                'value'  => 'someFormat2',
            ],
            //
            [
                'name'  => 'hashing.default',
                'value' => Stub::makeEmpty(Argon::class)
            ],
            [
                'name'   => 'hashing.default',
                'value'  => Stub::makeEmpty(HashingInterface::class)
            ],
            //
            [
                'batch' => [
                    'date.format'     => 'someFormat3',
                    'hashing.default' => Stub::makeEmpty(BCrypt::class),
                ]
            ],
            [
                'batch' => [
                    'date.format'     => 'someFormat4',
                    'hashing.default' => Stub::makeEmpty(HashingInterface::class),
                ]
            ],
        ];
    }

    /**
     * @dataProvider optionsProvider
     */
    public function optionsRetrieving(UnitTester $I, Example $example)
    {
        $I->wantToTest('Manager::options() | Set options by array with existing options');

        if (isset($example['batch'])) {
            $expected = $example['batch'];

            Manager::options($expected);

            /**
             * Remove extra keys
             */
            $actual = array_intersect_key(
                Manager::options(),
                array_flip(array_keys($expected))
            );

            $I->assertEquals($expected, $actual);
        } else {
            $optionName = $example['name'];
            $expected = $example['value'];

            Manager::options([$optionName => $expected]);

            $actual = Manager::options($optionName);

            $I->assertEquals($expected, $actual);
        }
    }
}
