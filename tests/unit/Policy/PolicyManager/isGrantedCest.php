<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Tests\unit\Policy\PolicyManager;

use http\Client\Curl\User;
use TimurFlush\Auth\Exception;
use TimurFlush\Auth\Policy\PolicyManager;
use TimurFlush\Auth\Role\RoleInterface;
use TimurFlush\Auth\Tests\Support\Auth\Policy\UserPolicy;
use TimurFlush\Auth\Tests\Support\Auth\Role\RoleModel;
use TimurFlush\Auth\Tests\Support\Auth\User\UserModel;
use TimurFlush\Auth\User\UserInterface;
use UnitTester;
use Mockery as m;
use TimurFlush\Auth\Role\RepositoryInterface as RoleRepository;

class isGrantedCest
{
    public function isGrantedSimplePolicyWithoutExtraArguments(UnitTester $I)
    {
        $I->wantToTest('PolicyManager::isGranted() | Simple policy without extra arguments');

        $userExecutor = new UserModel();
        $userExecutor->setId(1);

        $policyManager = new PolicyManager(
            m::mock(RoleRepository::class),
            fn() => 1
        );

        $policyManager = $policyManager->forExecutor($userExecutor);
        $policyManager->register(
            $policyAction = 'user:checkId',
            function (UserInterface $user) {
                return $user->getId() === 1;
            }
        );

        # Allowed id
        $I->assertTrue($policyManager->isGranted($policyAction));

        $userExecutor->setId(2);

        # Not allowed id
        $I->assertFalse($policyManager->isGranted($policyAction));
    }

    public function isGrantedSimplePolicyWithExtraArguments(UnitTester $I)
    {
        $I->wantToTest('PolicyManager::isGranted() | Simple policy with extra arguments');

        $userExecutor = new UserModel();

        $expectedFirstArgument = random_bytes(32);
        $expectedSecondArgument = random_int(1, 99);

        $policyManager = new PolicyManager(
            m::mock(RoleRepository::class),
            fn() => 1
        );

        $policyManager = $policyManager->forExecutor($userExecutor);
        $policyManager->register(
            $policyAction = 'user:someAction',
            function (
                UserInterface $user,
                $fArg,
                $sArg
            ) use (
                $I,
                $expectedFirstArgument,
                $expectedSecondArgument
            ) {
                return $fArg === $expectedFirstArgument && $sArg === $expectedSecondArgument;
            }
        );

        $I->assertTrue($policyManager->isGranted($policyAction, $expectedFirstArgument, $expectedSecondArgument));
        $I->assertFalse($policyManager->isGranted($policyAction, false, false));
        $I->assertFalse($policyManager->isGranted($policyAction, $expectedFirstArgument, false));
    }

    public function isGrantedSimplePolicyOnDoesNotAllowForGuestsException(UnitTester $I)
    {
        $I->wantToTest('PolicyManager::isGranted() | Simple policy on "does not allow for guests" exception');

        $policyManager = new PolicyManager(
            m::mock(RoleRepository::class),
            fn() => null
        );

        $policyManager->register(
            $policyAction = 'user:someAction',
            function (UserInterface $user) {
                // N O P
            }
        );

        $I->expectThrowable(
            new Exception('The policy ' . $policyAction . ' does not allow for guests'),
            function () use ($policyManager, $policyAction) {
                $policyManager->isGranted($policyAction);
            }
        );
    }

    public function isGrantedRolePolicyWithoutExtraArguments(UnitTester $I)
    {
        $I->wantToTest('PolicyManager::isGranted() | Role policy without extra arguments');

        $roleExecutor = new RoleModel();

        $roleExecutor->setId(3);

        $policyManager = new PolicyManager(
            m::mock(RoleRepository::class),
            fn() => 1
        );

        $policyManager = $policyManager->forExecutor($roleExecutor);
        $policyManager->register(
            $policyAction = 'role:checkId',
            function (RoleInterface $role) {
                return $role->getId() === 3;
            }
        );

        # Allowed id
        $I->assertTrue($policyManager->isGranted($policyAction));

        $roleExecutor->setId(4);

        # Not allowed id
        $I->assertFalse($policyManager->isGranted($policyAction));
    }

    public function isGrantedRolePolicyWithExtraArguments(UnitTester $I)
    {
        $I->wantToTest('PolicyManager::isGranted() | Role policy with extra arguments');

        $roleExecutor = new RoleModel();

        $expectedFirstArgument = random_bytes(32);
        $expectedSecondArgument = random_int(1, 99);

        $policyManager = new PolicyManager(
            m::mock(RoleRepository::class),
            fn() => 1
        );

        $policyManager = $policyManager->forExecutor($roleExecutor);
        $policyManager->register(
            $policyAction = 'role:someAction',
            function (
                RoleInterface $role,
                $fArg,
                $sArg
            ) use (
                $I,
                $expectedFirstArgument,
                $expectedSecondArgument
            ) {
                return $fArg === $expectedFirstArgument && $sArg === $expectedSecondArgument;
            }
        );

        $I->assertTrue($policyManager->isGranted($policyAction, $expectedFirstArgument, $expectedSecondArgument));
        $I->assertFalse($policyManager->isGranted($policyAction, false, false));
        $I->assertFalse($policyManager->isGranted($policyAction, $expectedFirstArgument, false));
    }

    public function isGrantedCheckingRolePolicyOnUserWithoutRoleViaSimplePolicy(UnitTester $I)
    {
        $I->wantToTest('PolicyManager::isGranted() | Checking Role Policy on User Without Role via simple policy');

        $userExecutor = new UserModel();

        $policyManager = new PolicyManager(
            m::mock(RoleRepository::class),
            fn() => 1
        );

        $policyManager = $policyManager->forExecutor($userExecutor);

        $policyManager->register(
            $policyAction = 'role:someAction',
            function (RoleInterface $role) {
                return true;
            }
        );

        $I->assertFalse($policyManager->isGranted($policyAction));
    }

    public function isGrantedCheckingRolePolicyOnUserWithRoleViaSimplePolicy(UnitTester $I)
    {
        $I->wantToTest('PolicyManager::isGranted() | Checking Role Policy on User with Role via simple policy');

        $roleMock1 = m::mock(RoleInterface::class);
        $roleMock1
            ->shouldReceive('getId')
            ->andReturn($expectedRoleID1 = random_int(1, 99));
        $roleMock1
            ->shouldReceive('getName')
            ->andReturn($expectedRoleName1 = '1' . random_bytes(32));

        $roleMock2 = m::mock(RoleInterface::class);
        $roleMock2
            ->shouldReceive('getId')
            ->andReturn($expectedRoleID2 = random_int(1, 99));
        $roleMock2
            ->shouldReceive('getName')
            ->andReturn($expectedRoleName2 = '2' . random_bytes(32));

        $roleRepositoryMock = m::mock(RoleRepository::class);
        $roleRepositoryMock
            ->shouldReceive('findByName')->withArgs([$expectedRoleName1])
            ->andReturn(null);
        $roleRepositoryMock
            ->shouldReceive('findByName')->withArgs([$expectedRoleName2])
            ->andReturn($roleMock2);

        $userExecutor = new UserModel();
        $userExecutor->addRoles([$roleMock1, $roleMock2]);

        $policyManager = new PolicyManager($roleRepositoryMock, fn() => 1);
        $policyManager = $policyManager->forExecutor($userExecutor);

        $policyManager->register(
            $policyAction = 'role:someAction',
            function (RoleInterface $role) use ($expectedRoleID2) {
                return $expectedRoleID2 === $role->getId();
            }
        );

        $I->assertTrue($policyManager->isGranted($policyAction));
    }

    public function isGrantedComplexPolicyWithoutExtraArguments(UnitTester $I)
    {
        $I->wantToTest('PolicyManager::isGranted() | Complex policy without extra arguments');

        $userExecutor = new UserModel();

        $policyManager = new PolicyManager(
            m::mock(RoleRepository::class),
            fn() => $userExecutor
        );

        $policyManager->assign(UserModel::class, UserPolicy::class);

        $userExecutor->setId(2);

        # Allowed id
        $I->assertTrue(
            $policyManager->isGranted('checkId', $userExecutor)
        );

        $userExecutor->setId(9);

        # Not allowed id
        $I->assertFalse(
            $policyManager->isGranted('checkId', $userExecutor)
        );
    }

    public function isGrantedComplexPolicyWithExtraArguments(UnitTester $I)
    {
        $I->wantToTest('PolicyManager::isGranted() | Complex policy with extra arguments');

        $userExecutor = new UserModel();

        $policyManager = new PolicyManager(
            m::mock(RoleRepository::class),
            fn() => $userExecutor
        );

        $policyManager->assign(UserModel::class, UserPolicy::class);

        $userExecutor->setId(3);

        $I->assertTrue(
            $policyManager->isGranted('withExtraArguments', $userExecutor, 1, 2)
        );

        $I->assertFalse(
            $policyManager->isGranted('withExtraArguments', $userExecutor, 1, false)
        );

        $I->assertFalse(
            $policyManager->isGranted('withExtraArguments', $userExecutor, false, 2)
        );

        $I->assertFalse(
            $policyManager->isGranted('withExtraArguments', $userExecutor, false, false)
        );
    }

    public function isGrantedComplexPolicyOnDoesNotAllowForGuestsException(UnitTester $I)
    {
        $I->wantToTest('PolicyManager::isGranted() | Complex policy on does not allow for guests exception');

        $policyManager = new PolicyManager(
            m::mock(RoleRepository::class),
            fn() => null
        );

        $policyManager->assign(UserInterface::class, UserPolicy::class);

        $policyAction = 'doesNotAllowForGuests';

        $I->expectThrowable(
            new Exception('The policy ' . (UserPolicy::class . '::' . $policyAction . '()'). ' does not allow for guests'),
            function () use ($policyManager, $policyAction) {
                $policyManager->isGranted($policyAction, UserInterface::class);
            }
        );
    }

    public function isGrantedCheckingRolePolicyOnUserWithoutRoleViaComplexPolicy(UnitTester $I)
    {
        $I->wantToTest('PolicyManager::isGranted() | Checking Role Policy on User Without Role via complex policy');

        $userExecutor = new UserModel();

        $policyManager = new PolicyManager(
            m::mock(RoleRepository::class),
            fn() => 1
        );

        $policyManager = $policyManager->forExecutor($userExecutor);

        $policyManager->assign(UserModel::class, UserPolicy::class);

        $I->assertFalse(
            $policyManager->isGranted(
                'checkRole',
                UserModel::class
            )
        );
    }

    public function isGrantedCheckingRolePolicyOnUserWithRoleViaComplexPolicy(UnitTester $I)
    {
        $I->wantToTest('PolicyManager::isGranted() | Checking Role Policy on User With Role via complex policy');

        $roleMock = m::mock(RoleInterface::class);
        $roleMock
            ->shouldReceive('getName')
            ->andReturn($roleName = 'admin');

        $roleRepositoryMock = m::mock(RoleRepository::class);
        $roleRepositoryMock
            ->shouldReceive('findByName')
            ->withArgs([$roleName])
            ->andReturn($roleMock);

        $userExecutor = new UserModel();
        $userExecutor->addRoles([$roleMock]);

        $policyManager = new PolicyManager(
            $roleRepositoryMock,
            fn() => 1
        );

        $policyManager = $policyManager->forExecutor($userExecutor);

        $policyManager->assign(UserModel::class, UserPolicy::class);

        $I->assertTrue(
            $policyManager->isGranted(
                'checkRole',
                UserModel::class
            )
        );
    }
}
