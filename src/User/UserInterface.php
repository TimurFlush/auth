<?php

declare(strict_types=1);

namespace TimurFlush\Auth\User;

use TimurFlush\Auth\Permission\InteractsWithPermissions;
use TimurFlush\Auth\Role\RoleInterface;

interface UserInterface extends InteractsWithPermissions
{
    /**
     * Sets an id of a user.
     *
     * @param int $id An id of a user.
     */
    public function setId(int $id);

    /**
     * Returns an id of a user.
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Returns an password of a user.
     *
     * @return string|null
     */
    public function getPassword(): ?string;

    /**
     * Checks user's credentials.
     *
     * @param array $credentials
     * @param bool  $allowWithoutPassword Determines whether it is allowed to check credentials
     *                                    without password.
     *
     * @return bool
     */
    public function checkCredentials(array $credentials, bool $allowWithoutPassword = false): bool;

    public function setBanStatus(?bool $status);

    public function getBanStatus(): bool;

    public function setActivationStatus(?bool $status);

    public function getActivationStatus(): bool;

    /**
     * Sets roles.
     *
     * @param array|RoleInterface[] $roles
     */
    public function addRoles(array $roles);

    /**
     * Returns roles.
     *
     * @return array
     */
    public function getRoles(): array;

    /**
     * Add a new permission.
     *
     * @param string      $permission A permission name.
     * @param bool        $value   A new value of a permission.
     */
    public function addPermission(string $permission, bool $value);

    /**
     * Checks a role existing
     *
     * @param $role
     */
    public function hasRole($role);

    /**
     * Rewrite a permission.
     *
     * @param string $permission        A permission name for rewriting.
     * @param bool   $newValue          A new value of a permission.
     * @param bool   $createIfNotExists Create new permission if not exists.
     */
    public function rewritePermission(string $permission, bool $newValue, bool $createIfNotExists);

    public function setApiToken(string $token);

    public function getApiToken(): ?string;
}
