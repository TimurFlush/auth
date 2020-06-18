<?php

namespace TimurFlush\Auth\Tests\Unit\Role\RoleModel;

use TimurFlush\Auth\Serializer\SerializerInterface;
use TimurFlush\Auth\Tests\Support\Auth\Role\RoleModel;
use UnitTester;
use Mockery as m;

class SerializerCest
{
    public function getSerializer(UnitTester $I)
    {
        $I->wantToTest('RoleModel::getSerializer()');

        $roleModel = new RoleModel();

        $I->assertInstanceOf(
            SerializerInterface::class,
            $roleModel->getSerializer(),
            'By default, the getSerializer() method must return some sort of serializer'
        );
    }

    public function setSerializer(UnitTester $I)
    {
        $I->wantToTest('RoleModel::setSerializer()');

        $roleModel = new RoleModel();

        $someSerializerMock = m::mock(SerializerInterface::class);

        $roleModel->setSerializer($someSerializerMock);

        $I->assertEquals(
            spl_object_hash($someSerializerMock),
            spl_object_hash($roleModel->getSerializer()),
            'Checking to see if the new serializer has been assigned'
        );
    }
}
