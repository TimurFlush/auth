<?php

declare(strict_types=1);

namespace TimurFlush\Auth\User;

interface UserInterface
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
}
