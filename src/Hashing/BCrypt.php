<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Hashing;

class BCrypt implements HashingInterface
{
    /**
     * @var int BCrypt cost.
     */
    protected int $cost;

    /**
     * @var string
     */
    protected string $staffPrefix = 'TFBCrypt';

    /**
     * BCrypt constructor.
     *
     * @param int $cost
     */
    public function __construct(int $cost = 10)
    {
        $this->cost = $cost;
    }

    /**
     * {@inheritDoc}
     */
    public function hash(string $original): string
    {
        return $this->staffPrefix . password_hash($original, PASSWORD_BCRYPT, [
            'cost' => $this->cost
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
