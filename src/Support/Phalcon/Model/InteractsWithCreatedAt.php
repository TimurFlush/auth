<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Support\Phalcon\Model;

use Carbon\Carbon;
use Phalcon\Mvc\Model\Behavior\Timestampable;
use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Manager;
use DateTimeInterface;

trait InteractsWithCreatedAt
{
    /**
     * @Column(type='timestamp', nullable=false)
     */
    protected ?string $created_at = null;

    /**
     * Apply created_at behavior.
     *
     * @return void
     */
    protected function applyCreatedAtBehavior(): void
    {
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
    }

    /**
     * Sets a creation time.
     *
     * @param DateTimeInterface $dateTime
     *
     * @return $this
     *
     * @throws InvalidArgumentException   Please see the method `TimurFlush\Auth\Manager::options()`
     * @throws \TimurFlush\Auth\Exception Please see the method `TimurFlush\Auth\Manager::options()`
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
     * @throws InvalidArgumentException   Please see the method `TimurFlush\Auth\Manager::options()`
     * @throws \TimurFlush\Auth\Exception Please see the method `TimurFlush\Auth\Manager::options()`
     */
    public function getCreatedAt(): ?Carbon
    {
        return isset($this->created_at)
            ? Carbon::createFromFormat(Manager::options('date.format'), $this->created_at)
            : null;
    }
}
