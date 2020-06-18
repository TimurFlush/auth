<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Tests\unit\Policy\PolicyInspector;

use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Policy\PolicyInspector;
use TimurFlush\Auth\Role\RoleInterface;
use TimurFlush\Auth\User\UserInterface;
use UnitTester;

class TotallyCest
{
    public function construct(UnitTester $I)
    {
        $I->wantToTest('PolicyInspector::__construct()');

        /*
         * Case 1: No arguments in policy
         */
        $policyName = 'someName';
        $I->expectThrowable(
            new InvalidArgumentException("The policy '" . $policyName . "' must have at least one argument"),
            function () use ($policyName) {
                $policy = function () {
                };

                $policyInspector = new PolicyInspector($policyName, $policy);
            }
        );
        $I->expectThrowable(
            new InvalidArgumentException("The policy '" . $policyName . "' must have at least one argument"),
            function () use ($policyName) {
                $class = new class {
                    public function someName()
                    {
                    }
                };

                $policyInspector = new PolicyInspector($policyName, [$class, $policyName]);
            }
        );

        /*
         * Case 2: Untyped property in first argument
         */
        $policyName = 'someName';
        $I->expectThrowable(
            new InvalidArgumentException("A first argument of the policy '" . $policyName . "' must be the typed property"),
            function () use ($policyName) {
                $policy = function ($untyped) {
                };

                $policyInspector = new PolicyInspector($policyName, $policy);
            }
        );
        $I->expectThrowable(
            new InvalidArgumentException("A first argument of the policy '" . $policyName . "' must be the typed property"),
            function () use ($policyName) {
                $class = new class {
                    public function someName($untyped)
                    {
                    }
                };

                $policyInspector = new PolicyInspector($policyName, [$class, $policyName]);
            }
        );

        /*
         * Case 3: Untyped property in first argument
         */
        $policyName = 'someName';
        $I->expectThrowable(
            new InvalidArgumentException(sprintf(
                "The first argument of the policy '%s' must refer to the %s or the %s",
                $policyName,
                UserInterface::class,
                RoleInterface::class
            )),
            function () use ($policyName) {
                $policy = function (string $untyped) {
                };

                $policyInspector = new PolicyInspector($policyName, $policy);
            }
        );
        $I->expectThrowable(
            new InvalidArgumentException(sprintf(
                "The first argument of the policy '%s' must refer to the %s or the %s",
                $policyName,
                UserInterface::class,
                RoleInterface::class
            )),
            function () use ($policyName) {
                $class = new class {
                    public function someName(string $untyped)
                    {
                    }
                };

                $policyInspector = new PolicyInspector($policyName, [$class, $policyName]);
            }
        );
    }

    public function isUserPolicy(UnitTester $I)
    {
        $I->wantToTest('PolicyInspector::isUserPolicy()');

        $policyName = 'someName';

        /*
         * Case 1: Closure
         */
        $closure = function (UserInterface $user) {
        };

        $policyInspector = new PolicyInspector($policyName, $closure);

        $I->assertTrue($policyInspector->isUserPolicy());
        $I->assertFalse($policyInspector->isRolePolicy());
        $I->assertFalse($policyInspector->isAllowNull());

        /*
         * Case 2: Callable
         */
        $class = new class {
            public function someName(UserInterface $user)
            {
            }
        };

        $policyInspector = new PolicyInspector($policyName, [$class, $policyName]);

        $I->assertTrue($policyInspector->isUserPolicy());
        $I->assertFalse($policyInspector->isRolePolicy());
        $I->assertFalse($policyInspector->isAllowNull());
    }

    public function isRolePolicy(UnitTester $I)
    {
        $I->wantToTest('PolicyInspector::isRolePolicy()');

        $policyName = 'someName';

        /*
         * Case 1: closure
         */
        $closure = function (RoleInterface $role) {
        };

        $policyInspector = new PolicyInspector($policyName, $closure);

        $I->assertFalse($policyInspector->isUserPolicy());
        $I->assertTrue($policyInspector->isRolePolicy());
        $I->assertFalse($policyInspector->isAllowNull());

        /*
         * Case 2: Callable
         */
        $class = new class {
            public function someName(RoleInterface $role)
            {
            }
        };

        $policyInspector = new PolicyInspector($policyName, [$class, $policyName]);

        $I->assertFalse($policyInspector->isUserPolicy());
        $I->assertTrue($policyInspector->isRolePolicy());
        $I->assertFalse($policyInspector->isAllowNull());
    }

    public function isAllowsNull(UnitTester $I)
    {
        $I->wantToTest('PolicyInspector::isAllowsNull()');

        $policyName = 'someName';

        /*
         * Case 1: Closure
         */
        $closure = function (?UserInterface $user) {
        };

        $policyInspector = new PolicyInspector($policyName, $closure);

        $I->assertTrue($policyInspector->isUserPolicy());
        $I->assertFalse($policyInspector->isRolePolicy());
        $I->assertTrue($policyInspector->isAllowNull());

        /*
         * Case 2: Callable
         */
        $class = new class {
            public function someName(?UserInterface $user)
            {
            }
        };

        $policyInspector = new PolicyInspector($policyName, [$class, $policyName]);

        $I->assertTrue($policyInspector->isUserPolicy());
        $I->assertFalse($policyInspector->isRolePolicy());
        $I->assertTrue($policyInspector->isAllowNull());
    }
}
