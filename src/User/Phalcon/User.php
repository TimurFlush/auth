<?php

declare(strict_types=1);

namespace TimurFlush\Auth\User\Phalcon;

use Phalcon\Mvc\Model;
use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Manager;
use TimurFlush\Auth\Support\Phalcon\Model\InteractsWithCreatedAt;
use TimurFlush\Auth\Support\Phalcon\Model\InteractsWithUpdatedAt;
use TimurFlush\Auth\User\UserInterface;

abstract class User extends Model implements UserInterface
{
    use InteractsWithCreatedAt;
    use InteractsWithUpdatedAt;

    /**
     * @Column(type='biginteger', nullable=false)
     * @Primary
     * @Identity
     */
    protected ?int $id = null;

    /**
     * @Column(type='varchar', nullable=true)
     */
    protected ?string $password = null;

    /**
     * Initialize method.
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->setSource('users');
        $this->useDynamicUpdate(true);

        $this->applyCreatedAtBehavior();
        $this->applyUpdatedAtBehavior();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     *
     * @throws InvalidArgumentException   If an identity is zero and it's forbidden.
     * @throws InvalidArgumentException   If an identity is negative and it's forbidden.
     * @throws InvalidArgumentException   Please see the method `TimurFlush\Auth\Manager::options()`
     * @throws \TimurFlush\Auth\Exception Please see the method `TimurFlush\Auth\Manager::options()`
     */
    public function setId(int $id)
    {
        if ($id === 0 && Manager::options('userModel.allowZeroId')) {
            throw new InvalidArgumentException('An identity cannot be zero.');
        } elseif ($id < 0 && Manager::options('allowNegativeId')) {
            throw new InvalidArgumentException('An identity cannot be negative.');
        }

        $this->id = $id;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Sets a password of a user.
     *
     * @param string $password
     *
     * @return $this
     *
     * @throws InvalidArgumentException   Please see the method `TimurFlush\Auth\Manager::options()`
     * @throws \TimurFlush\Auth\Exception Please see the method `TimurFlush\Auth\Manager::options()`
     */
    public function setPassword(string $password)
    {
        if (empty($password)) {
            throw new InvalidArgumentException('A password cannot be empty.');
        }

        $this->password = Manager::options('hashing.default')->hash($password);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }
}
