<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Event;

class BeforeResolving extends ResolvingAbstract implements EventInterface
{
    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'beforeResolving';
    }
}
