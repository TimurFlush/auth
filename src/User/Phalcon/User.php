<?php

declare(strict_types=1);

namespace TimurFlush\Auth\User\Phalcon;

use Carbon\Carbon;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Behavior\Timestampable;
use TimurFlush\Auth\Exception\InvalidArgumentException;
use DateTimeInterface;
use TimurFlush\Auth\Manager;
use TimurFlush\Auth\User\UserInterface;

class User extends Model implements UserInterface
{
    /**
     * @Column(type='biginteger', nullable=false)
     * @Primary
     * @Identity
     */
    protected ?int $id = null;

    /**
     * @Column(type='varchar', nullable=false)
     */
    protected ?string $login = null;

    /**
     * @Column(type='varchar', nullable=true)
     */
    protected ?string $password = null;

    /**
     * @Column(type='timestamp', nullable=false)
     */
    protected ?string $created_at = null;

    /**
     * @Column(type='timestamp', nullable=true)
     */
    protected ?string $updated_at = null;

    public function initialize()
    {
        $this->setSource('users');
        $this->useDynamicUpdate(true);

        $this->addBehavior(
            new Timestampable(
                [
                    'beforeValidationOnCreate' => [
                        'field' => 'created_at',
                        'generator' => function () {
                            $imm = new \DateTimeImmutable('NOW');
                            return $imm->format(Manager::options('date.format'));
                        }
                    ]
                ]
            )
        );

        $this->addBehavior(
            new Timestampable(
                [
                    'beforeValidationOnUpdate' => [
                        'field' => 'updated_at',
                        'generator' => function () {
                            $imm = new \DateTimeImmutable('NOW');
                            return $imm->format(Manager::options('date.format'));
                        }
                    ]
                ]
            )
        );
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     *
     * @throws InvalidArgumentException If an identity is zero and it's forbidden.
     * @throws InvalidArgumentException If an identity is negative and it's forbidden.
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
     * @throws InvalidArgumentException If a password is empty.
     */
    public function setPassword(string $password)
    {
        if (empty($password)) {
            throw new InvalidArgumentException('A password cannot be empty.');
        }

        $this->password = Manager::getHashing()->hash($password);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Sets a creation time.
     *
     * @param DateTimeInterface $dateTime
     *
     * @return $this
     *
     * @throws InvalidArgumentException Please see the method `Manager::options()`
     */
    public function setCreatedAt(DateTimeInterface $dateTime)
    {
        $this->created_at = $dateTime->format(Manager::options('date.format'));
        return $this;
    }

    /**
     * Returns a creation time.
     *
     * @return Carbon|null
     *
     * @throws InvalidArgumentException Please see the method `Manager::options()`
     */
    public function getCreatedAt(): ?Carbon
    {
        return isset($this->created_at)
            ? Carbon::createFromFormat(Manager::options('date.format'), $this->created_at)
            : null;
    }

    /**
     * Sets an update time.
     *
     * @param DateTimeInterface $dateTime
     *
     * @return $this
     *
     * @throws InvalidArgumentException Please see the method `Manager::options()`
     */
    public function setUpdatedAt(DateTimeInterface $dateTime)
    {
        $this->updated_at = $dateTime->format(Manager::options('date.format'));
        return $this;
    }

    /**
     * Returns an update time.
     *
     * @return Carbon|null
     *
     * @throws InvalidArgumentException Please see the method `Manager::options()`
     */
    public function getUpdatedAt(): ?Carbon
    {
        return isset($this->updated_at)
            ? Carbon::createFromFormat(Manager::options('date.format'), $this->updated_at)
            : null;
    }
}
