<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Policy;

interface PolicyInterface
{
    /**
     * Returns a prefix of the current policy.
     *
     * @return string
     */
    public function getPrefix(): string;

    /**
     * Returns a map, which will be used on matching
     * the policy action for a user.
     *
     * @return array
     */
    public function getUserMap(): array;

    /**
     * Returns a map, which will be used on matching
     * the policy action for a role.
     *
     * @return array
     */
    public function getRoleMap(): array;

    /**
     * Returns a map, which will be used for to allow
     * access to the policy action without a permission.
     *
     * @return array
     */
    public function getForceMap(): array;
}
