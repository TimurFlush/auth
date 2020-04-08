<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Accessor;

use TimurFlush\Auth\User\UserInterface;

interface StatefulAccessor extends StatelessAccessor
{
    /**
     * Attempt to login a user by his credentials.
     *
     * @param array $credentials
     * @param bool  $remember
     *
     * @return bool
     */
    public function attemptLogin(array $credentials, bool $remember = false): bool;

    /**
     * Login a user by the UserInterface object.
     *
     * @param UserInterface $user
     * @param bool          $remember
     *
     * @return bool
     */
    public function loginByUser(UserInterface $user, bool $remember = false): bool;

    /**
     * Login a user by his identity.
     *
     * @param int  $userId
     * @param bool $remember
     *
     * @return bool
     */
    public function loginById(int $userId, bool $remember = false): bool;

    /**
     * Login a user by the UserInterface object only for this request.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function loginByUserOnce(UserInterface $user): bool;

    /**
     * Login a user by his identity only for this request.
     *
     * @param int $userId
     *
     * @return bool
     */
    public function loginByIdOnce(int $userId): bool;

    /**
     * Determines whether the current authentication is through "remember"
     *
     * @return bool
     */
    public function isAuthViaRemember(): bool;

    /**
     * Logout the user.
     *
     * @return void
     */
    public function logout(): void;
}
