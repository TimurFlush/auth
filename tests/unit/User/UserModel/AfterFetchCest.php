<?php

namespace TimurFlush\Auth\Tests\Unit\User\UserModel;

use TimurFlush\Auth\Role\RoleInterface;
use TimurFlush\Auth\Serializer\SerializerInterface;
use TimurFlush\Auth\User\UserModel;
use UnitTester;
use Mockery as m;

class AfterFetchCest
{
    public function afterFetchOnRoles(UnitTester $I)
    {
        $I->wantToTest('UserModel::afterFetch() | On roles');

        $roleMock = m::mock(RoleInterface::class);
        $roleMock
            ->shouldReceive('getName')
            ->andReturn($roleName = 'someRole');

        $jsonSerializerMock = m::mock(SerializerInterface::class);
        $jsonSerializerMock
            ->shouldReceive('serialize')
            ->withArgs(function (array $arg) use ($I, $roleName) {
                $I->assertContains(
                    $roleName,
                    $arg
                );

                return true;
            })
            ->andReturn($serialized = '{serialized}');

        $jsonSerializerMock
            ->shouldReceive('unserialize')
            ->withArgs(function (string $arg) use ($I, $serialized) {
                $I->assertEquals(
                    $serialized,
                    $arg
                );

                return true;
            })
            ->andReturn([$roleName]);

        replaceAuthManager(
            null,
            null,
            null,
            $jsonSerializerMock
        );

        $userModel = new UserModel();
        $userModel->addRoles([$roleMock]);

        $userModel->beforeValidation();
        $userModel->afterFetch();

        $I->assertContains(
            $roleName,
            $userModel->getRoles()
        );
    }

    public function afterFetchOnPermissions(UnitTester $I)
    {
        $I->wantToTest('UserModel::afterFetch() | On permissions');

        $permissionName = 'somePermission';

        $jsonSerializerMock = m::mock(SerializerInterface::class);
        $jsonSerializerMock
            ->shouldReceive('serialize')
            ->withArgs(function (array $arg) use ($I, $permissionName) {
                $I->assertArrayHasKey(
                    $permissionName,
                    $arg
                );

                return true;
            })
            ->andReturn($serialized = '{serialized}');

        $jsonSerializerMock
            ->shouldReceive('unserialize')
            ->withArgs(function (string $arg) use ($I, $serialized) {
                $I->assertEquals(
                    $serialized,
                    $arg
                );

                return true;
            })
            ->andReturn([$permissionName => true]);

        replaceAuthManager(
            null,
            null,
            null,
            $jsonSerializerMock
        );

        $userModel = new UserModel();
        $userModel->addPermission($permissionName, true);

        $userModel->beforeValidation();
        $userModel->afterFetch();

        $I->assertArrayHasKey(
            $permissionName,
            $userModel->getPermissions()
        );
    }
}
