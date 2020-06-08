<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Hashing;

use TimurFlush\Auth\Exception;
use TimurFlush\Auth\Exception\InvalidArgumentException;

class Whirlpool implements HashingInterface
{
    /**
     * @var int
     */
    protected int $saltLength = 32;

    /**
     * @var string
     */
    protected string $staffPrefix = 'TFWhirlpool';

    /**
     * Whirlpool constructor.
     *
     * @throws Exception If your php build does not support the whirlpool algorithm.
     */
    public function __construct()
    {
        // @codeCoverageIgnoreStart
        if (!in_array('whirlpool', hash_algos())) {
            throw new Exception('Your php build does not support the whirlpool algorithm.');
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException Please see the method `static::createHash()`
     * @throws \Exception               Please see the method `static::createHash()`
     */
    public function hash(string $original): string
    {
        return $this->staffPrefix . $this->createHash($original);
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException Please see the method `static::createHash()`
     * @throws \Exception               Please see the method `static::createHash()`
     */
    public function check(string $original, string $hashed): bool
    {
        $hashed = str_replace(
            $this->staffPrefix,
            '',
            $hashed
        );

        $salt = substr($hashed, 0, $this->saltLength);

        return Utils::safelyCompare(
            $this->createHash($original, $salt),
            $hashed
        );
    }

    /**
     * @param string $original
     * @param string|null $salt
     *
     * @return string
     *
     * @throws InvalidArgumentException Please see the method `TimurFlush\Auth\Utils::randomString()`
     * @throws \Exception               Please see the method `TimurFlush\Auth\Utils::randomString()`
     */
    protected function createHash(string $original, string $salt = null): string
    {
        if ($salt === null) {
            $salt = Utils::randomString($this->saltLength);
        }

        return sprintf(
            '%s.%s',
            $salt,
            hash('whirlpool', $salt . $original)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function isMyHash(string $hash): bool
    {
        return strpos($hash, $this->staffPrefix) === 0;
    }
}
