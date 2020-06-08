<?php

namespace TimurFlush\Auth\Tests\Unit\Checker\Activation;

use TimurFlush\Auth\Checker\Activation;
use TimurFlush\Auth\User\UserInterface;
use UnitTester;
use Mockery as m;

class OnValidationCest
{
    public function onValidation(UnitTester $I)
    {
        $I->wantToTest('Activation::onValidation()');

        $checker = new Activation();

        $userMock1 = m::mock(UserInterface::class);
        $userMock1
            ->shouldReceive('getActivationStatus')
            ->andReturn(true);

        $userMock2 = m::mock(UserInterface::class);
        $userMock2
            ->shouldReceive('getActivationStatus')
            ->andReturn(false);

        $I->assertTrue($checker->onAuthentication($userMock1));
        $I->assertFalse($checker->onAuthentication($userMock2));
    }
}
