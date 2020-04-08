<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Policy;

use Closure;
use TimurFlush\Auth\Role\RoleInterface;
use TimurFlush\Auth\User\UserInterface;

interface PolicyManagerInterface
{
    /**
     * This method should clone current instance and to replace user resolver.
     *
     * @param UserInterface|RoleInterface $executor
     *
     * @return PolicyManager
     */
    public function forExecutor($executor): PolicyManager;

    /**
     * Registers a simple policy.
     *
     * @param string   $name
     * @param Closure  $callback
     * @param bool     $replaceIfExists
     *
     * @return void
     */
    public function register(string $name, Closure $callback, bool $replaceIfExists = false): void;

    /**
     * Registers a complex policy and assigns owner to it.
     *
     * @param string $owner       An owner.
     * @param string $policyClass An policy class.
     *
     * @return void
     */
    public function assign(string $owner, string $policyClass): void;

    /**
     * Determines whether the current authenticated user can be authorized for
     * the specified policy.
     *
     * <code>
     * // The example for a simple policy
     * $manager->can('someOwner:create', SomeOwner::class'); //creating
     * $manager->can('someOwner:update', $someOwner); //updating
     * </code>
     *
     * <code>
     * // The example for a complex policy
     * $manager->can('create', SomeOwner::class); //creating
     * $manager->can('update', $someOwner); //updating
     * </code>
     *
     * @param string $owner
     * @param string $policyAction
     * @param mixed  $extraArguments
     *
     * @return bool
     */
    public function isGranted(string $policyAction, $owner, ...$extraArguments): bool;
}
