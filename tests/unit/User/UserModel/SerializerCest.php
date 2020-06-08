<?php

namespace TimurFlush\Auth\Tests\Unit\User\UserModel;

use TimurFlush\Auth\Serializer\SerializerInterface;
use TimurFlush\Auth\User\UserModel;
use UnitTester;
use Mockery as m;

class SerializerCest
{
    public function getSerializer(UnitTester $I)
    {
        $I->wantToTest('UserModel::getSerializer()');

        $userModel = new UserModel();

        $I->assertInstanceOf(
            SerializerInterface::class,
            $userModel->getSerializer(),
            'By default, the getSerializer() method must return some sort of serializer'
        );
    }

    public function setSerializer(UnitTester $I)
    {
        $I->wantToTest('UserModel::setSerializer()');

        $userModel = new UserModel();

        $someSerializerMock = m::mock(SerializerInterface::class);

        $userModel->setSerializer($someSerializerMock);

        $I->assertEquals(
            spl_object_hash($someSerializerMock),
            spl_object_hash($userModel->getSerializer()),
            'Checking to see if the new serializer has been assigned'
        );
    }
}
