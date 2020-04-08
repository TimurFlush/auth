<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Permission;

interface InteractsWithPermissions
{
    /**
     * Sets permissions.
     *
     * @param array $permissions
     */
    public function setPermissions(array $permissions);

    /**
     * Returns permissions.
     *
     * @return array
     */
    public function getPermissions(): array;

    /**
     * Remove a permission.
     *
     * @param string $permission A permission name.
     */
    public function removePermission(string $permission);

    /**
     * Determines whether a permissions holder
     * can perform a permission.
     *
     * @param string        $permission
     * @param callable|null $callback          A name of the simple policy or callback.
     * @param mixed         $callbackArguments
     *
     * @return bool
     */
    public function isPermitted(string $permission, callable $callback = null, ...$callbackArguments): bool;
}
