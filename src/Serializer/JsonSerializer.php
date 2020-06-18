<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Serializer;

use TimurFlush\Auth\Exception\SerializerException;

class JsonSerializer implements SerializerInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws \TimurFlush\Auth\Exception\SerializerException
     */
    public function serialize(array $data): string
    {
        try {
            return json_encode($data, JSON_THROW_ON_ERROR);
            //@codeCoverageIgnoreStart
        } catch (\JsonException $exception) {
            throw new SerializerException($exception->getMessage());
            //@codeCoverageIgnoreEnd
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws \TimurFlush\Auth\Exception\SerializerException
     */
    public function unserialize(string $serialized): array
    {
        try {
            return json_decode($serialized, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new SerializerException($exception->getMessage());
        }
    }
}
