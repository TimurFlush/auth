<?php

use Phalcon\Di;
use TimurFlush\Auth\Manager as AuthManager;
use Mockery as m;
use TimurFlush\Auth\User\RepositoryInterface as UserRepositoryInterface;
use TimurFlush\Auth\Activation\RepositoryInterface as ActivationRepositoryInterface;
use TimurFlush\Auth\Role\RepositoryInterface as RoleRepositoryInterface;
use TimurFlush\Auth\Serializer\JsonSerializer;
use Phalcon\Events\Manager as EventsManager;

require_once __DIR__ . '/../vendor/autoload.php';

\Codeception\Util\Autoload::addNamespace(
    'TimurFlush\Auth\Tests\Support\Auth',
    __DIR__ . '/_support/Auth'
);

$di = new Di();

$di->setShared('modelsManager', function () {
    return new \Phalcon\Mvc\Model\Manager();
});

$di->setShared('eventsManager', function() {
    $eventsManager = new EventsManager();

    return $eventsManager;
});

$di->setShared('authManager', function () {
    $authManager = new AuthManager(
        m::mock(UserRepositoryInterface::class),
        m::mock(ActivationRepositoryInterface::class),
        m::mock(RoleRepositoryInterface::class),
        new JsonSerializer(),
        $this->get('eventsManager')
    );

    return $authManager;
});

function replaceAuthManager(
    $userRepository = null,
    $activationRepository = null,
    $roleRepository = null,
    $serializer = null,
    $eventsManager = null
) {
    $di = Di::getDefault();

    if ($userRepository === null) {
        $userRepository = $di->get('authManager')->getUserRepository();
    }

    if ($activationRepository === null) {
        $activationRepository = $di->get('authManager')->getActivationRepository();
    }

    if ($roleRepository === null) {
        $roleRepository = $di->get('authManager')->getRoleRepository();
    }

    if ($serializer === null) {
        $serializer = $di->get('authManager')->getSerializer();
    }

    if ($eventsManager === null) {
        $eventsManager = $di->get('authManager')->getEventsManager();
    }

    $di->remove('authManager');

    $di->setShared('authManager', new AuthManager(
        $userRepository,
        $activationRepository,
        $roleRepository,
        $serializer,
        $eventsManager
    ));
}
