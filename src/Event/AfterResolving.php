<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Event;

class AfterResolving extends ResolvingAbstract implements EventInterface
{
    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'afterResolving';
    }
}
