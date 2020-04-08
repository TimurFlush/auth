<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Event;

class BeforeRegister implements EventInterface
{
    protected array $credentials = [];

    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'beforeRegister';
    }

    /**
     * Returns credentials which associated with this event.
     */
    public function getCredentials(): array
    {
        return $this->credentials;
    }
}
