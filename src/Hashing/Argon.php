<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Hashing;

use TimurFlush\Auth\Exception;
use TimurFlush\Auth\Exception\InvalidArgumentException;

class Argon implements HashingInterface
{
    public const TYPE_2I = '2i';
    public const TYPE_2D = '2d';

    /**
     * @var string
     */
    protected string $type;

    /**
     * @var int
     */
    protected int $memoryCost;

    /**
     * @var int
     */
    protected int $timeCost;

    /**
     * @var int
     */
    protected int $threads;

    /**
     * @var string
     */
    protected string $staffPrefix = 'TFArgon';

    /**
     * Argon constructor.
     *
     * @param string $type       Argon type.
     * @param int    $memoryCost
     * @param int    $timeCost
     * @param int    $threads
     *
     * @throws Exception                If your php build does not support the argon algorithm.
     * @throws InvalidArgumentException If passed an invalid Argon type.
     */
    public function __construct(
        string $type,
        int $memoryCost = null,
        int $timeCost = null,
        int $threads = null
    ) {
        // @codeCoverageIgnoreStart
        if (!defined('PASSWORD_ARGON2I') || !defined('PASSWORD_ARGON2ID')) {
            throw new Exception('Your php build does not support the argon algorithm.');
        }
        // @codeCoverageIgnoreEnd

        if (!in_array($type, $types = [static::TYPE_2D, static::TYPE_2I])) {
            throw new InvalidArgumentException(
                'Passed an invalid Argon type. Available types: ' . join(', ', $types)
            );
        }

        if ($memoryCost === null) {
            $memoryCost = PASSWORD_ARGON2_DEFAULT_MEMORY_COST;
        }

        if ($timeCost === null) {
            $timeCost = PASSWORD_ARGON2_DEFAULT_TIME_COST;
        }

        if ($threads === null) {
            $threads = PASSWORD_ARGON2_DEFAULT_THREADS;
        }

        $this->type = $type;
        $this->memoryCost = $memoryCost;
        $this->timeCost = $timeCost;
        $this->threads = $threads;
    }

    /**
     * {@inheritDoc}
     */
    public function hash(string $original): string
    {
        switch ($this->type) {
            case static::TYPE_2I:
                $type = 'argon2i';
                break;

            default:
            case static::TYPE_2D:
                $type = 'argon2id';
                break;
        }

        return $this->staffPrefix . password_hash($original, $type, [
            'memory_cost' => $this->memoryCost,
            'time_cost' => $this->timeCost,
            'threads' => $this->threads
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function check(string $original, string $hashed): bool
    {
        return password_verify(
            $original,
            str_replace(
                $this->staffPrefix,
                '',
                $hashed
            )
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
