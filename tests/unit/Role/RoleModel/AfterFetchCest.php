<?php

namespace TimurFlush\Auth\Tests\Unit\Role\RoleModel;

use TimurFlush\Auth\Serializer\SerializerInterface;
use TimurFlush\Auth\Tests\Support\Auth\Role\RoleModel;
use UnitTester;
use Mockery as m;

class AfterFetchCest
{
    public function afterFetchOnPermissions(UnitTester $I)
    {
        $I->wantToTest('RoleModel::afterFetch() | On permissions');

        $permissionName = 'somePermission';

        $jsonSerializerMock = m::mock(SerializerInterface::class);
        $jsonSerializerMock
            ->shouldReceive('serialize')
            ->withArgs(function (array $arg) use ($I, $permissionName) {
                $I->assertContains(
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
            ->andReturn([$permissionName]);

        replaceAuthManager(
            null,
            null,
            null,
            $jsonSerializerMock
        );

        $roleModel = new RoleModel();
        $roleModel->addPermission($permissionName);

        $roleModel->beforeValidation();
        $roleModel->afterFetch();

        $I->assertContains(
            $permissionName,
            $roleModel->getPermissions()
        );
    }
}
