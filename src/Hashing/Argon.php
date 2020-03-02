<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Hashing;

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
     * Argon constructor.
     *
     * @param string $type       Argon type.
     * @param int    $memoryCost
     * @param int    $timeCost
     * @param int    $threads
     *
     * @throws InvalidArgumentException If passed an invalid Argon type.
     */
    public function __construct(
        string $type,
        int $memoryCost = PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
        int $timeCost = PASSWORD_ARGON2_DEFAULT_TIME_COST,
        int $threads = PASSWORD_ARGON2_DEFAULT_THREADS
    ) {
        if (!in_array($type, $types = [static::TYPE_2D, static::TYPE_2I])) {
            throw new InvalidArgumentException(
                'Passed an invalid Argon type. Available types: ' . join(', ', $types)
            );
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
        if ($this->type === static::TYPE_2I) {
            $type = PASSWORD_ARGON2I;
        } elseif ($this->type === static::TYPE_2D) {
            $type = PASSWORD_ARGON2ID;
        } else {
            throw new \RuntimeException('Wrong argon type: ' . $this->type);
        }

        return password_hash($original, $type, [
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
        return password_verify($original, $hashed);
    }
}
