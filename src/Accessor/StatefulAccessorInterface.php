<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Accessor;

use TimurFlush\Auth\Checker\OptionalCheckerInterface;
use TimurFlush\Auth\User\UserInterface;

interface StatefulAccessorInterface extends AccessorInterface
{
    /**
     * Returns a module name.
     *
     * @return string
     */
    public function getModuleName(): string;

    /**
     * Attempt to login a user by his credentials.
     *
     * @param array                      $credentials
     * @param bool                       $remember
     * @param OptionalCheckerInterface[] $extraCheckers
     * @param bool                       $replaceCheckers
     *
     * @return bool
     */
    public function attemptLogin(
        array $credentials,
        bool $remember = false,
        array $extraCheckers = [],
        bool $replaceCheckers = false
    ): bool;

    /**
     * Login a user by the UserInterface object.
     *
     * @param UserInterface              $user
     * @param bool                       $remember
     * @param OptionalCheckerInterface[] $extraCheckers
     * @param bool                       $replaceCheckers
     *
     * @return bool
     */
    public function loginByUser(
        UserInterface $user,
        bool $remember = false,
        array $extraCheckers = [],
        bool $replaceCheckers = false
    ): bool;

    /**
     * Login a user by his identity.
     *
     * @param int                        $userId
     * @param bool                       $remember
     * @param OptionalCheckerInterface[] $extraCheckers
     * @param bool                       $replaceCheckers
     *
     * @return bool
     */
    public function loginById(
        int $userId,
        bool $remember = false,
        array $extraCheckers = [],
        bool $replaceCheckers = false
    ): bool;

    /**
     * Login a user by the UserInterface object only for this request.
     *
     * ATTENTION:
     * Use with caution. This method should not check the user via checkers.
     * To prevent this behavior, you can pass on the necessary checkers with a second argument.
     *
     * @param UserInterface              $user
     * @param OptionalCheckerInterface[] $extraCheckers
     *
     * @return bool
     */
    public function loginByUserOnce(UserInterface $user, array $extraCheckers = []): bool;

    /**
     * Login a user by his identity only for this request.
     *
     * ATTENTION:
     * Use with caution. This method should not check the user via checkers.
     * To prevent this behavior, you can pass on the necessary checkers with a second argument.
     *
     * @param int                        $userId
     * @param OptionalCheckerInterface[] $extraCheckers
     *
     * @return bool
     */
    public function loginByIdOnce(int $userId, array $extraCheckers = []): bool;

    /**
     * Determines whether the current authentication is through "remember"
     *
     * @return bool
     */
    public function isAuthViaRemember(): bool;

    /**
     * Determines whether this authentication attempt is temporary.
     *
     * @return bool
     */
    public function isAuthViaOnce(): bool;

    /**
     * Logout the user.
     *
     * @return void
     */
    public function logout(): void;
}
