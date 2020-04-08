<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Event\Object;

use TimurFlush\Auth\Event\EventInterface;

class BeforeRegister implements EventInterface
{
    /**
     * @var array
     */
    protected array $credentials = [];

    /**
     * AfterRegister constructor.
     *
     * @param array $credentials
     */
    public function __construct(array $credentials) {
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
     *
     * @return array
     */
    public function getCredentials(): array
    {
        return $this->credentials;
    }
}
