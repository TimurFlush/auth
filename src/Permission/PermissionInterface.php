<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Permission;

interface PermissionInterface
{
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
    public function getName(): string;
}
