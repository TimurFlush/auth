<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Hashing;

use Closure;

interface LocatorInterface
{
    /**
     * Register a closure function which will be provide a HashingInterface object.
     *
     * @param Closure $closure
     *
     * @return void
     */
    public static function register(Closure $closure): void;

    /**
     * Locate a hashing type by hash.
     *
     * @param string $hash
     *
     * @return HashingInterface|null
     */
    public static function locate(string $hash): ?HashingInterface;

    /**
     * Resets the state.
     *
     * @return void
     */
    public static function reset(): void;
}
