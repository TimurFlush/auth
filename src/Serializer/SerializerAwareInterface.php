<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Serializer;

interface SerializerAwareInterface
{
    /**
     * Sets the permissions serializer instance.
     *
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer);

    /**
     * Returns the permissions serializer instance.
     *
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface;
}
