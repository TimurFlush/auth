<?php

declare(strict_types=1);

namespace TimurFlush\Auth\User;

interface RepositoryInterface
{
    /**
     * Creates an empty user.
     *
     * @param array $credentials
     * @param bool  $activate
     *
     * @return UserInterface
     */
    public function createNewUser(array $credentials, bool $activate = false): UserInterface;

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
     * Searches a user by his api token
     *
     * @param string $apiToken
     *
     * @return UserInterface|null
     */
    public function findByApiToken(string $apiToken): ?UserInterface;

    /**
     * Saves a user to persistent storage.
     *
     * @param UserInterface $user
     */
    public function save(UserInterface $user): void;
}
