<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Support\Model;

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
        /**
         * @var Manager $authManager
         */
        $authManager = $this
            ->getDI()
            ->getShared('authManager');

        $this->addBehavior(
            new Timestampable(
                [
                    'beforeValidationOnCreate' => [
                        'field' => 'created_at',
                        'generator' => function () use ($authManager) {
                            $imm = new \DateTimeImmutable('NOW');
                            return $imm->format($authManager->getSqlDateFormat());
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
        /**
         * @var Manager $authManager
         */
        $authManager = $this
            ->getDI()
            ->getShared('authManager');

        $this->created_at = $dateTime->format($authManager->getSqlDateFormat());
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
        /**
         * @var Manager $authManager
         */
        $authManager = $this
            ->getDI()
            ->getShared('authManager');

        return isset($this->created_at)
            ? Carbon::createFromFormat($authManager->getSqlDateFormat(), $this->created_at)
            : null;
    }
}
