<?php

namespace TimurFlush\Auth\Tests\Unit\Checker\Ban;

use TimurFlush\Auth\Checker\Ban;
use TimurFlush\Auth\User\UserInterface;
use UnitTester;
use Mockery as m;

class OnAuthenticationCest
{
    public function onAuthentication(UnitTester $I)
    {
        $I->wantToTest('Ban::onAuthentication()');

        $checker = new Ban();

        $userMock1 = m::mock(UserInterface::class);
        $userMock1
            ->shouldReceive('getBanStatus')
            ->andReturn(false);

        $userMock2 = m::mock(UserInterface::class);
        $userMock2
            ->shouldReceive('getBanStatus')
            ->andReturn(true);

        $I->assertTrue($checker->onAuthentication($userMock1));
        $I->assertFalse($checker->onAuthentication($userMock2));
    }
}
