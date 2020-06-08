<?php

namespace TimurFlush\Auth\Tests\Unit\User\UserModel;

use TimurFlush\Auth\Role\RoleInterface;
use TimurFlush\Auth\Serializer\SerializerInterface;
use TimurFlush\Auth\User\UserModel;
use UnitTester;
use Mockery as m;

class BeforeValidationCest
{
    public function beforeValidationOnRoles(UnitTester $I)
    {
        $I->wantToTest('UserModel::beforeValidation() | On roles');

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
            ->andReturn($exp = '{serialized}');

        replaceAuthManager(
            null,
            null,
            null,
            $jsonSerializerMock
        );

        $userModel = new UserModel();

        $userModel->addRoles([$roleMock]);
        $userModel->beforeValidation();

        $reflObject = new \ReflectionObject($userModel);

        $reflRoles = $reflObject->getProperty('roles');
        $reflRoles->setAccessible(true);

        $I->assertEquals(
            $exp,
            $reflRoles->getValue($userModel)
        );
    }

    public function beforeValidationOnPermissions(UnitTester $I)
    {
        $I->wantToTest('UserModel::beforeValidation() | On permissions');

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
            ->andReturn($exp = '{serialized}');

        replaceAuthManager(
            null,
            null,
            null,
            $jsonSerializerMock
        );

        $userModel = new UserModel();

        $userModel->addPermission($permissionName, true);
        $userModel->beforeValidation();

        $reflObject = new \ReflectionObject($userModel);

        $reflPermissions = $reflObject->getProperty('permissions');
        $reflPermissions->setAccessible(true);

        $I->assertEquals(
            $exp,
            $reflPermissions->getValue($userModel)
        );
    }
}
