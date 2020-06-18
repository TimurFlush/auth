<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Tests\unit\Policy\PolicyManager;

use TimurFlush\Auth\Exception;
use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Policy\PolicyManager;
use UnitTester;
use Mockery as m;
use TimurFlush\Auth\Role\RepositoryInterface as RoleRepository;

class RegisterCest
{
    public function register(UnitTester $I)
    {
        $I->wantToTest('PolicyManager::register()');

        $policyManager = new PolicyManager(
            m::mock(RoleRepository::class),
            fn() => 1
        );

        /*
         * Case 1: Adding
         */
        $closure = fn() => 1;

        $policyManager->register($expectedName = 'some:unique', $closure);

        $refl = new \ReflectionObject($policyManager);
        $refl = $refl->getProperty('policies');
        $refl->setAccessible(true);

        $reflectedPolicies = $refl->getValue($policyManager);

        $I->assertArrayHasKey(
            $expectedName,
            $reflectedPolicies
        );

        $I->assertEquals(
            spl_object_hash($reflectedPolicies[$expectedName]),
            spl_object_hash($closure)
        );

        /*
         * Case 2: Unauthorized replace
         */
        $I->expectThrowable(
            new Exception('Attempt to replace an existing policy without permission for replacement.'),
            function () use ($policyManager) {
                $policyManager->register('some:name', fn() => 1, false);
                $policyManager->register('some:name', fn() => 2, false);
            }
        );

        /*
         * Case 3: Name correctness check
         */
        $I->expectThrowable(
            new InvalidArgumentException('A simple policy name should be contains the `:` symbol'),
            function () use ($policyManager) {
                $policyManager->register('someInvalidName1', fn() => 1, false);
            }
        );
        $I->expectThrowable(
            new InvalidArgumentException('A simple policy name should be contains the `:` symbol'),
            function () use ($policyManager) {
                $policyManager->register('some_invalid-name-2', fn() => 2, false);
            }
        );
    }
}
