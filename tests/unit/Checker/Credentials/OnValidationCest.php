<?php

namespace TimurFlush\Auth\Tests\Unit\Checker\Credentials;

use TimurFlush\Auth\Checker\Activation;
use TimurFlush\Auth\Checker\Credentials;
use TimurFlush\Auth\User\UserInterface;
use UnitTester;
use Mockery as m;

class OnValidationCest
{
    public function onValidation(UnitTester $I)
    {
        $I->wantToTest('Credentials::onValidation()');

        $checker = new Credentials([]);

        $userMock = m::mock(UserInterface::class);

        $I->assertFalse($checker->onValidation($userMock));
    }
}
