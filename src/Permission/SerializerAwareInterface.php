<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Permission;

interface SerializerAwareInterface
{
    /**
     * Sets the permissions serializer instance.
     *
     * @param SerializerInterface $serializer
     */
    public function setPermissionsSerializer(SerializerInterface $serializer);

    /**
     * Returns the permissions serializer instance.
     *
     * @return SerializerInterface
     */
    public function getPermissionsSerializer(): SerializerInterface;
}
