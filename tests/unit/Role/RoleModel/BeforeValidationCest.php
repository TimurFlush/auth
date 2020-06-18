<?php

namespace TimurFlush\Auth\Tests\Unit\Role\RoleModel;

use TimurFlush\Auth\Serializer\SerializerInterface;
use TimurFlush\Auth\Tests\Support\Auth\Role\RoleModel;
use UnitTester;
use Mockery as m;

class BeforeValidationCest
{
    public function beforeValidationOnPermissions(UnitTester $I)
    {
        $I->wantToTest('RoleModel::beforeValidation() | On permissions');

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
            ->andReturn($exp = '{serialized}');

        replaceAuthManager(
            null,
            null,
            null,
            $jsonSerializerMock
        );

        $roleModel = new RoleModel();

        $roleModel->addPermission($permissionName);
        $roleModel->beforeValidation();

        $reflObject = new \ReflectionObject($roleModel);

        $reflPermissions = $reflObject->getProperty('permissions');
        $reflPermissions->setAccessible(true);

        $I->assertEquals(
            $exp,
            $reflPermissions->getValue($roleModel)
        );
    }
}
