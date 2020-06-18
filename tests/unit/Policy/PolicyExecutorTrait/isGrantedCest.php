<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Tests\unit\Policy\PolicyInspector;

use Phalcon\Di;
use Phalcon\Di\DiInterface;
use TimurFlush\Auth\Policy\PolicyExecutorTrait;
use TimurFlush\Auth\Policy\PolicyManager;
use TimurFlush\Auth\Role\RoleInterface;
use TimurFlush\Auth\Tests\Support\Auth\Policy\PolicyExecutorTrait\UserExecutor;
use TimurFlush\Auth\User\UserInterface;
use UnitTester;
use Mockery as m;
use TimurFlush\Auth\Role\RepositoryInterface as RoleRepository;

class isGrantedCest
{
    public function isGranted(UnitTester $I)
    {
        $I->wantToTest('PolicyExecutorTrait::isGranted()');

        $di = Di::getDefault();

        $someClass = new UserExecutor();

        if ($di->has('policyManager')) {
            $di->remove('policyManager');
        }

        $policyAction1 = 'some:policy1';
        $policyAction2 = 'some:policy2';

        $di->setShared('policyManager', function () use ($policyAction1, $policyAction2, $I) {
            $policyManager = new PolicyManager(
                m::mock(RoleRepository::class),
                fn() => 1
            );

            $policyManager->register($policyAction1, function (UserInterface $executor) use ($I) {
                $I->assertInstanceOf(
                    UserExecutor::class,
                    $executor
                );

                return true;
            });

            $policyManager->register($policyAction2, function (UserInterface $executor) use ($I) {
                $I->assertInstanceOf(
                    UserExecutor::class,
                    $executor
                );

                return false;
            });

            return $policyManager;
        });

        $I->assertTrue(
            $someClass->isGranted($policyAction1)
        );

        $I->assertFalse(
            $someClass->isGranted($policyAction2)
        );
    }
}
