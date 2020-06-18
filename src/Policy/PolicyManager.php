<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Policy;

use TimurFlush\Auth\Exception;
use Closure;
use TimurFlush\Auth\Role\RepositoryInterface as RoleRepository;
use TimurFlush\Auth\Role\RoleInterface;
use TimurFlush\Auth\User\UserInterface;
use TimurFlush\Auth\Exception\InvalidArgumentException;

class PolicyManager implements PolicyManagerInterface
{
    protected Closure $executorResolver;

    protected array $assignMap = [];

    protected array $policies = [];

    protected RoleRepository $roleRepository;

    /**
     * PolicyManager constructor.
     *
     * @param RoleRepository $roleRepository
     * @param Closure        $executorResolver A closure which must be return UserInterface or RoleInterface
     */
    public function __construct(RoleRepository $roleRepository, Closure $executorResolver)
    {
        $this->roleRepository = $roleRepository;
        $this->executorResolver = $executorResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function forExecutor($executor): PolicyManager
    {
        if (
            ($executor instanceof UserInterface) === false &&
            ($executor instanceof RoleInterface) === false
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    "The 'executor' argument must be UserInterface or RoleInterface, %s given",
                    is_object($executor) ? get_class($executor) : gettype($executor)
                )
            );
        }

        $static = clone $this;
        $static->executorResolver = (fn() => $executor);

        return $static;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \TimurFlush\Auth\Exception                          If an attempt to replace an existing policy
     *                                                             is detected.
     *
     * @throws \TimurFlush\Auth\Exception\InvalidArgumentException If a simple policy name does not contain
     *                                                             the `:` symbol.
     */
    public function register(string $name, Closure $callback, bool $replaceIfExists = false): void
    {
        if (isset($this->policies[$name]) && $replaceIfExists === false) {
            throw new Exception(
                'Attempt to replace an existing policy without permission for replacement.'
            );
        }

        if (!$this->isSimplePolicy($name)) {
            throw new InvalidArgumentException(
                'A simple policy name should be contains the `:` symbol'
            );
        }

        $this->policies[$name] = $callback;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \TimurFlush\Auth\Exception\InvalidArgumentException If a specified policy class does not exist.
     *
     * @throws \TimurFlush\Auth\Exception\InvalidArgumentException If a specified policy does not implement
     *                                                             the \TimurFlush\Auth\Policy\PolicyInterface.
     */
    public function assign(string $owner, string $policyClass): void
    {
        if (!class_exists($policyClass)) {
            throw new InvalidArgumentException(
                "The policy class '" . $policyClass . "' does not exist."
            );
        }

        $this->assignMap[$owner] = $policyClass;
    }

    /**
     * Resolve a complex policy by policy owner.
     *
     * @param string|object $owner
     *
     * @return object
     *
     * @throws \TimurFlush\Auth\Exception\InvalidArgumentException If the owner of a resolvable policy
     *                                                             is not a string or an object.
     *
     * @throws \TimurFlush\Auth\Exception\InvalidArgumentException If a complex policy which assigned to
     *                                                             a specified owner does not exist.
     */
    protected function resolveComplexPolicy($owner): object
    {
        if (!is_string($owner) && !is_object($owner)) {
            //@codeCoverageIgnoreStart
            $owner = is_object($owner) ? get_class($owner) : gettype($owner);

            throw new InvalidArgumentException(
                sprintf(
                    'The owner of a resolvable policy must be a string or an object, %s given',
                    $owner
                )
            );
            //@codeCoverageIgnoreEnd
        }

        if (is_object($owner)) {
            $owner = get_class($owner);
        }

        if (isset($this->policies[$owner])) {
            return $this->policies[$owner];
        }

        if (isset($this->assignMap[$owner])) {
            // TODO : Auto-wiring via DI in Phalcon 4.1/4.2
            return $this->policies[$owner] = new $this->assignMap[$owner];
        }

        //@codeCoverageIgnoreStart
        throw new InvalidArgumentException(
            "A complex policy which assigned to the owner '" . $owner . "' does not exist."
        );
        //@codeCoverageIgnoreEnd
    }

    /**
     * Resolves an executor from the user resolver.
     *
     * @return UserInterface|RoleInterface|null
     *
     * @throws \TimurFlush\Auth\Exception If the user resolver did not return null or the
     *                                    TimurFlush\Auth\UserModel\UserInterface
     */
    protected function resolveExecutor()
    {
        $executor = ($this->executorResolver)();

        if (
            $executor instanceof UserInterface === false &&
            $executor instanceof RoleInterface === false &&
            $executor !== null
        ) {
            //@codeCoverageIgnoreStart
            throw new Exception(
                sprintf(
                    "The executor resolver must return null, %s or %s, %s given",
                    UserInterface::class,
                    RoleInterface::class,
                    is_object($executor) ? get_class($executor) : gettype($executor)
                )
            );
            //@codeCoverageIgnoreEnd
        }

        return $executor;
    }

    /**
     * Determines whether a policy name is simple.
     *
     * @param string $name
     *
     * @return bool
     */
    protected function isSimplePolicy(string $name): bool
    {
        return strpos($name, ':') > 0 && strlen($name) > 1 && substr_count($name, ':') === 1;
        //return (bool)preg_match('/.+:.+/', $name);
    }

    /**
     * Resolves a simple policy by policy action.
     *
     * @param string $policyAction
     *
     * @return Closure
     *
     * @throws \TimurFlush\Auth\Exception\InvalidArgumentException If a specified policy does not exist.
     */
    protected function resolveSimplePolicy(string $policyAction): Closure
    {
        if (!isset($this->policies[$policyAction])) {
            //@codeCoverageIgnoreStart
            throw new InvalidArgumentException(
                "A simple policy with the action '" . $policyAction . "' does not exist"
            );
            //@codeCoverageIgnoreEnd
        }

        return $this->policies[$policyAction];
    }

    /**
     * {@inheritDoc}
     *
     * @throws \TimurFlush\Auth\Exception                          Please see the method `static::resolveUser()`
     * @throws \TimurFlush\Auth\Exception\InvalidArgumentException Please see the method `\TimurFlush\Auth\Policy\PolicyInspector::__construct()`
     * @throws \ReflectionException                                Please see the method `\TimurFlush\Auth\Policy\PolicyInspector::__construct()`
     * @throws \TimurFlush\Auth\Exception                          If a resolved policy does not allow for guests.
     */
    public function isGranted(string $policyAction, $owner = null, ...$extraArguments): bool
    {
        $args = func_get_args();

        if ($this->isSimplePolicy($policyAction)) {
            $callback = $this->resolveSimplePolicy($policyAction);
        } else {
            $callback = [
                $this->resolveComplexPolicy($owner),
                $policyAction
            ];

            $policyAction = get_class($callback[0]) . '::' . $policyAction .  '()';
        }

        /**
         * Next, we need to inspect the resolved policy.
         */
        $policyInspector = new PolicyInspector($policyAction, $callback);

        $executor = $this->resolveExecutor();

        $callArray = [];

        if ($policyInspector->isUserPolicy()) {
            if ($executor === null && !$policyInspector->isAllowNull()) {
                throw new Exception('The policy ' . $policyAction . ' does not allow for guests');
            }

            $callArray = [$executor];
        } elseif ($policyInspector->isRolePolicy()) {
            if ($executor instanceof RoleInterface) {
                // N O P
            } elseif ($executor instanceof UserInterface) {
                foreach ($executor->getRoles() as $roleName) {
                    $getRole = $this->roleRepository->findByName($roleName);

                    if ($getRole === null) {
                        continue;
                    }

                    $effect = $this->forExecutor($getRole)->isGranted(...$args);

                    if ($effect === true) {
                        return $effect;
                    }
                }

                return false;
            } else {
                //@codeCoverageIgnoreStart
                return false;
                //@codeCoverageIgnoreEnd
            }

            $callArray = [$executor];
        }

        if (is_object($executor)) {
            $callArray[] = $owner;
        }

        array_push($callArray, ...$extraArguments);

        return call_user_func_array($callback, $callArray);
    }
}
