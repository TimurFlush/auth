<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Serializer;

interface SerializerInterface
{
    /**
     * Serializes data.
     *
     * @param array $data Associative array.
     *
     * @return string
     */
    public function serialize(array $data): string;

    /**
     * Unserializes data.
     *
     * @param string $serialized
     *
     * @return array
     */
    public function unserialize(string $serialized): array;
}
