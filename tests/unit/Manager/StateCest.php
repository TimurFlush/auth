<?php

namespace TimurFlush\Auth\Tests\Unit\Manager;

use TimurFlush\Auth\Hashing\BCrypt;
use TimurFlush\Auth\Hashing\HashingInterface;
use TimurFlush\Auth\Manager;
use UnitTester;

class StateCest
{
    protected function extractOptions()
    {
        $refl = new \ReflectionClass(Manager::class);

        $optionsProperty = $refl->getProperty('options');
        $optionsProperty->setAccessible(true);

        return $optionsProperty->getValue();
    }

    protected function extractInitialized()
    {
        $refl = new \ReflectionClass(Manager::class);

        $optionsProperty = $refl->getProperty('initialized');
        $optionsProperty->setAccessible(true);

        return $optionsProperty->getValue();
    }

    protected function describeDefaultOptions(): array
    {
        return [
            'userModel.allowZeroId' => [
                'type'  => 'bool',
                'value' => false
            ],
            'userModel.allowNegativeId' => [
                'type'  => 'bool',
                'value' => false
            ],
            'hashing.default' => [
                'type'  => HashingInterface::class,
                'value' => new BCrypt()
            ],
            'date.format' => [
                'type'  => 'string',
                'value' => 'Y-m-d H:i:s.uO'
            ],
        ];
    }

    public function beforeInitializing(UnitTester $I)
    {
        $I->wantToTest('Options::$options | Check if the `Manager::$options` property is null before initialization');
        $I->assertNull($this->extractOptions());
    }

    public function resetState(UnitTester $I)
    {
        $I->wantToTest('Options::reset() | Check if the `Manager::$options` property is null after reset');

        Manager::initialize();
        $I->assertIsArray($this->extractOptions());
        $I->assertTrue($this->extractInitialized());

        Manager::reset();
        $I->assertNull($this->extractOptions());
        $I->assertFalse($this->extractInitialized());
    }

    public function defaultOptionsViaReflection(UnitTester $I)
    {
        $I->wantToTest('Options::$options | Check default options via reflection');

        Manager::initialize();

        $expected = $this->describeDefaultOptions();
        $actual   = $this->extractOptions();

        $I->assertEquals($expected, $actual);

        Manager::reset();
    }

    public function defaultOptionsViaDirectCall(UnitTester $I)
    {
        $I->wantToTest('Options::$options | Check default option via direct call');

        Manager::initialize();

        $expected = array_map(
            fn($value) => $value['value'],
            $this->describeDefaultOptions()
        );
        $actual   = Manager::options();

        $I->assertEquals($expected, $actual);

        Manager::reset();
    }

    public function notWipeState(UnitTester $I)
    {
        $I->wantToTest('Options::initialize() | Initialization should not wipe out the state, if the manager is already initialized.');

        Manager::initialize();

        Manager::options(
            [
                'date.format' => ($expected = 'someFormat')
            ]
        );

        $I->assertEquals($expected, Manager::options('date.format'));

        Manager::initialize();

        $I->assertEquals($expected, Manager::options('date.format'));

        Manager::reset();
    }
}
