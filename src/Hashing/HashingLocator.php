<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Hashing;

use Closure;
use TimurFlush\Auth\Exception;
use TimurFlush\Auth\Manager;

class HashingLocator implements HashingLocatorInterface
{
    /**
     * @var Closure[]
     */
    protected static ?array $pool;

    /**
     * {@inheritDoc}
     */
    public static function register(Closure $closure): void
    {
        if (!is_array(static::$pool)) {
            static::$pool = [];
        }

        static::$pool[] = $closure;
    }

    /**
     * Returns the default hashing.
     *
     * @return BCrypt
     */
    public static function getDefaultHashing(): BCrypt
    {
        return new BCrypt();
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If some element of locator pool does not implement the HashingInterface
     */
    public static function locate(string $hash): ?HashingInterface
    {
        /**
         * Avoid the overhead
         */
        if (empty($hash)) {
            return null;
        }

        $defaultHashing = static::getDefaultHashing();

        if ($defaultHashing->isMyHash($hash)) {
            return $defaultHashing;
        }

        /**
         * Avoid the overhead
         */
        $pool = static::$pool ?? [];

        foreach ($pool as $closure) {
            $hashing = $closure();

            if ($hashing instanceof HashingInterface) {
                if ($hashing->isMyHash($hash)) {
                    return $hashing;
                }

                continue;
            }

            throw new Exception('An element of locator pool must be implement the HashingInterface');
        }

        return null;
    }
}
