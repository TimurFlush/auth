<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Support\Model;

use Carbon\Carbon;
use Phalcon\Mvc\Model\Behavior\Timestampable;
use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Manager;
use DateTimeInterface;

trait InteractsWithUpdatedAt
{
    /**
     * @Column(type='timestamp', nullable=true)
     */
    protected ?string $updated_at = null;

    /**
     * Apply updated_at behavior.
     *
     * @return void
     */
    protected function applyUpdatedAtBehavior(): void
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
                    'beforeValidationOnUpdate' => [
                        'field' => 'updated_at',
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
     * Sets an update time.
     *
     * @param DateTimeInterface $dateTime
     *
     * @return $this
     */
    public function setUpdatedAt(DateTimeInterface $dateTime)
    {
        /**
         * @var Manager $authManager
         */
        $authManager = $this
            ->getDI()
            ->getShared('authManager');

        $this->updated_at = $dateTime->format($authManager->getSqlDateFormat());
        return $this;
    }

    /**
     * Returns an update time.
     *
     * @return Carbon|null
     */
    public function getUpdatedAt(): ?Carbon
    {
        /**
         * @var Manager $authManager
         */
        $authManager = $this
            ->getDI()
            ->getShared('authManager');

        return isset($this->updated_at)
            ? Carbon::createFromFormat($authManager->getSqlDateFormat(), $this->updated_at)
            : null;
    }
}
