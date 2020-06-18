<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Tests\unit\Policy\PolicyManager;

use TimurFlush\Auth\Policy\PolicyManager;
use UnitTester;
use Mockery as m;
use TimurFlush\Auth\Role\RepositoryInterface as RoleRepository;

class ConstructCest
{
    public function construct(UnitTester $I)
    {
        $I->wantToTest('PolicyManager::__construct()');

        $policyManager = new PolicyManager(
            $roleRepositoryMock = m::mock(RoleRepository::class),
            $executorResolver = fn() => 1
        );

        $refl = new \ReflectionObject($policyManager);

        $roleRepositoryProperty = $refl->getProperty('roleRepository');
        $roleRepositoryProperty->setAccessible(true);
        $roleRepositoryValue = $roleRepositoryProperty->getValue($policyManager);

        $I->assertEquals(
            spl_object_hash($roleRepositoryMock),
            spl_object_hash($roleRepositoryValue)
        );

        $executorResolverProperty = $refl->getProperty('executorResolver');
        $executorResolverProperty->setAccessible(true);
        $executorResolverValue = $executorResolverProperty->getValue($policyManager);

        $I->assertEquals(
            spl_object_hash($executorResolver),
            spl_object_hash($executorResolverValue)
        );
    }
}
