<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Tests\unit\Policy\PolicyManager;

use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Policy\PolicyManager;
use TimurFlush\Auth\Tests\Support\Auth\Policy\UserPolicy;
use UnitTester;
use Mockery as m;
use TimurFlush\Auth\Role\RepositoryInterface as RoleRepository;

class AssignCest
{
    public function assign(UnitTester $I)
    {
        $I->wantToTest('PolicyManager::assign()');

        $policyManager = new PolicyManager(
            m::mock(RoleRepository::class),
            $executorResolver = fn() => 1
        );

        /*
         * Case 1: A policy does not exists
         */
        $policyClass = 'nonExistingNamespace\nonExistingClass';
        $I->expectThrowable(
            new InvalidArgumentException("The policy class '" . $policyClass . "' does not exist."),
            function () use ($policyManager, $policyClass) {
                $policyManager->assign('someOwner', $policyClass);
            }
        );

        /*
         * Case 2: Adding
         */
        $policyManager->assign($expectedOwner = 'someOwner', $expectedPolicyClass = UserPolicy::class);

        $refl = new \ReflectionObject($policyManager);
        $refl = $refl->getProperty('assignMap');
        $refl->setAccessible(true);

        $reflectedAssignMap = $refl->getValue($policyManager);

        $I->assertArrayHasKey(
            $expectedOwner,
            $reflectedAssignMap
        );

        $I->assertEquals(
            $expectedPolicyClass,
            $reflectedAssignMap[$expectedOwner]
        );
    }
}
