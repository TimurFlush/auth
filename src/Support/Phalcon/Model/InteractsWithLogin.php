<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Support\Phalcon\Model;

use Carbon\Carbon;
use Phalcon\Mvc\Model\Behavior\Timestampable;
use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Manager;
use DateTimeInterface;

trait InteractsWithLogin
{
    /**
     * @Column(type='timestamp', nullable=true)
     */
    protected ?string $updated_at = null;

    /**
     * Sets an update time.
     *
     * @param DateTimeInterface $dateTime
     *
     * @return $this
     *
     * @throws InvalidArgumentException   Please see the method `TimurFlush\Auth\Manager::options()`
     * @throws \TimurFlush\Auth\Exception Please see the method `TimurFlush\Auth\Manager::options()`
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
     * @throws InvalidArgumentException   Please see the method `TimurFlush\Auth\Manager::options()`
     * @throws \TimurFlush\Auth\Exception Please see the method `TimurFlush\Auth\Manager::options()`
     */
    public function getUpdatedAt(): ?Carbon
    {
        return isset($this->updated_at)
            ? Carbon::createFromFormat(Manager::options('date.format'), $this->updated_at)
            : null;
    }
}
