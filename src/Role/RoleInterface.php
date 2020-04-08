<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Role;

use TimurFlush\Auth\Permission\InteractsWithPermissions;

interface RoleInterface extends InteractsWithPermissions
{
    /**
     * Sets an id.
     *
     * @param int $id
     */
    public function setId(int $id);

    /**
     * Returns an id.
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Sets a name.
     *
     * @param string $name
     */
    public function setName(string $name);

    /**
     * Returns a name.
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Add a new permission.
     *
     * @param string      $permission A permission name.
     */
    public function addPermission(string $permission);
}
