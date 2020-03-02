<?php

declare(strict_types=1);

namespace TimurFlush\Auth\User;

interface RepositoryInterface
{
    /**
     * Creates an empty user.
     *
     * @return UserInterface
     */
    public function createNewUser(): UserInterface;

    /**
     * Searches a user by his identity.
     *
     * @param int $id
     *
     * @return UserInterface|null
     */
    public function findById(int $id): ?UserInterface;

    /**
     * Searches a user by his credentials.
     *
     * @param array $credentials
     *
     * @return UserInterface|null
     */
    public function findByCredentials(array $credentials): ?UserInterface;

    /**
     * Saves a user to persistent storage.
     *
     * @param UserInterface $user
     */
    public function save(UserInterface $user): void;
}
