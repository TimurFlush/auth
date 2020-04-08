<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Permission;

use TimurFlush\Auth\Exception\PermissionsSerializerException;

class JsonSerializer implements SerializerInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws \TimurFlush\Auth\Exception\PermissionsSerializerException
     */
    public function serialize(array $permissions): string
    {
        try {
            return json_encode($permissions, JSON_THROW_ON_ERROR, 2);
        } catch (\JsonException $exception) {
            throw new PermissionsSerializerException($exception->getMessage());
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws \TimurFlush\Auth\Exception\PermissionsSerializerException
     */
    public function unserialize(string $serialized): array
    {
        try {
            return json_decode($serialized, true, 2, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new PermissionsSerializerException($exception->getMessage());
        }
    }
}
