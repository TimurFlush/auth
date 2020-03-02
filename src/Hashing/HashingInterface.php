<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Hashing;

interface HashingInterface
{
    /**
     * Hashes a string.
     *
     * @param string $original Original string.
     *
     * @return string Hashed string.
     */
    public function hash(string $original): string;

    /**
     * Determines whether the original string refers to the hashed string.
     *
     * @param string $original Original string.
     * @param string $hashed   Hashed string.
     *
     * @return bool
     */
    public function check(string $original, string $hashed): bool;
}
