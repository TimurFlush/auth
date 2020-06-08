<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Event;

use Phalcon\Events\ManagerInterface as EventsManager;

trait Fireable
{
    protected EventsManager $eventsManager;

    /**
     * Returns the internal event manager
     *
     * @return void
     */
    public function setEventsManager(EventsManager $manager): void
    {
        $this->eventsManager = $manager;
    }

    /**
     * Returns the internal event manager
     */
    public function getEventsManager(): ?EventsManager
    {
        return $this->eventsManager;
    }

    /**
     * Fires an event.
     *
     * @return mixed
     */
    public function fireEvent(EventInterface $event)
    {
        if (isset($this->eventsManager)) {
            return $this->eventsManager->fire('auth:' . $event->getName(), $this, $event);
        }
    }
}
