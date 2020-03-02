<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Hashing;

use TimurFlush\Auth\Exception\InvalidArgumentException;

class Utils
{
    /**
     * Returns a random string.
     *
     * @param int $length A length of a random string.
     *
     * @return string
     *
     * @throws InvalidArgumentException If a length isn't positive.
     * @throws \Exception               If it was not possible to gather sufficient entropy.
     */
    public static function randomString(int $length): string
    {
        if ($length <= 0) {
            throw new InvalidArgumentException('A length must be positive.');
        }

        $supportLength = null;

        if ($length % 2 === 1) {
            $supportLength = $length + 1;
        }

        $string = bin2hex(
            random_bytes($supportLength ?? $length)
        );

        return substr($string, 0, $length);
    }
}
