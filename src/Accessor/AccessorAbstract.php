<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Accessor;

use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\ManagerInterface as EventsManager;
use TimurFlush\Auth\Event\Fireable;
use TimurFlush\Auth\Session\SessionInterface;
use TimurFlush\Auth\User\UserInterface;

abstract class AccessorAbstract implements AccessorInterface, EventsAwareInterface
{
    use Fireable;

    protected UserInterface $user;

    protected SessionInterface $session;

    protected EventsManager $eventsManager;

    /**
     * {@inheritDoc}
     */
    public function isAuth(): bool
    {
        return isset($this->user);
    }

    /**
     * {@inheritDoc}
     */
    public function isGuest(): bool
    {
        return !$this->isAuth();
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getUser(): ?UserInterface
    {
        return isset($this->user) ? $this->user : null;
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function setSession(?SessionInterface $session)
    {
        $this->session = $session;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSession(): ?SessionInterface
    {
        return isset($this->session) ? $this->session : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getUserId(): ?int
    {
        return $this->isAuth()
            ? $this->getUser()->getId()
            : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getSessionId(): ?string
    {
        return $this->isAuth()
            ? $this->getSession()->getId()
            : null;
    }
}
