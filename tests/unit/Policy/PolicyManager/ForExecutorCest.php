<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Tests\unit\Policy\PolicyManager;

use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Policy\PolicyManager;
use TimurFlush\Auth\Policy\PolicyManagerInterface;
use TimurFlush\Auth\User\UserInterface;
use UnitTester;
use Mockery as m;
use TimurFlush\Auth\Role\RepositoryInterface as RoleRepository;

class ForExecutorCest
{
    public function construct(UnitTester $I)
    {
        $I->wantToTest('PolicyManager::forExecutor()');

        $roleRepositoryMock = m::mock(RoleRepository::class);

        /*
         * Case 1: Invalid returned value of closure
         */
        $closure = fn() => 1;
        $policyManager = new PolicyManager(
            $roleRepositoryMock,
            $closure
        );

        $I->expectThrowable(
            new InvalidArgumentException("The 'executor' argument must be UserInterface or RoleInterface, integer given"),
            function () use ($policyManager) {
                $policyManager->forExecutor(1);
            }
        );

        /*
         * Case 2: Normal mode
         */
        $executorMock = m::mock(
            UserInterface::class
        );
        $closure = fn() => $executorMock;

        $policyManager = new PolicyManager(
            $roleRepositoryMock,
            $closure
        );

        $newExecutor = $policyManager->forExecutor($executorMock);

        $I->assertInstanceOf(
            PolicyManagerInterface::class,
            $newExecutor
        );
    }
}
