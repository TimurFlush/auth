<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Session;

use TimurFlush\Auth\User\UserInterface;

interface RepositoryInterface
{
    /**
     * Creates an empty session.
     *
     * @param UserInterface $user
     * @param bool          $remember
     *
     * @return SessionInterface
     */
    public function createNewSession(UserInterface $user, bool $remember = false): SessionInterface;

    /**
     * Searches a session by her identity.
     *
     * @param string $id
     *
     * @return SessionInterface|null
     */
    public function findById(string $id): ?SessionInterface;

    /**
     * Saves a user to persistent storage.
     *
     * @param SessionInterface $session
     */
    public function save(SessionInterface $session): void;
}
