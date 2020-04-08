<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Permission;

interface PermissionsSerializerInterface
{
    /**
     * Serializes permissions.
     *
     * @param array $permissions Associative array.
     *
     * @return string
     */
    public function serialize(array $permissions): string;

    /**
     * Unserializes permissions.
     *
     * @param string $serialized
     *
     * @return array
     */
    public function unserialize(string $serialized): array;
}
