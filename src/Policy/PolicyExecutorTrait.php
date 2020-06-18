<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Policy;

use Phalcon\Di\DiInterface;
use TimurFlush\Auth\Exception;

trait PolicyExecutorTrait
{
    abstract public function getDI(): DiInterface;

    /**
     * This method should proxy calls to `TimurFlush\Auth\Policy\PolicyManager::isGranted()`
     *
     * @param string        $policyAction
     * @param string|object $owner
     * @param mixed         $extraArguments
     *
     * @return bool
     *
     * @throws \TimurFlush\Auth\Exception                          If unable to resolve the policy manager from the DI
     * @throws \TimurFlush\Auth\Exception                          Please see the method `TimurFlush\Auth\Policy\PolicyManager::isGranted()`
     * @throws \TimurFlush\Auth\Exception\InvalidArgumentException Please see the method `TimurFlush\Auth\Policy\PolicyManager::isGranted()`
     * @throws \ReflectionException                                Please see the method `TimurFlush\Auth\Policy\PolicyManager::isGranted()`
     */
    public function isGranted(string $policyAction, $owner = null, ...$extraArguments): bool
    {
        $di = $this->getDI();

        if (!$di->has('policyManager')) {
            //@codeCoverageIgnoreStart
            throw new Exception(
                "The dependency injection container does not contain the service 'policyManager'"
            );
            //@codeCoverageIgnoreEnd
        }

        /** @var PolicyManagerInterface $policyManager */
        $policyManager = $di->getShared('policyManager');

        return $policyManager
            ->forExecutor($this)
            ->isGranted($policyAction, $owner, ...$extraArguments);
    }
}
