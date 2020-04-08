<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Event;

interface EventInterface
{
    /**
     * Returns an event name.
     */
    public function getName(): string;
}
