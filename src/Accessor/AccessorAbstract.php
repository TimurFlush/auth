<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Accessor;

use TimurFlush\Auth\Event\ManagerInterface as EventsManager;
use TimurFlush\Auth\Session\SessionInterface;
use TimurFlush\Auth\User\UserInterface;

abstract class AccessorInterfaceAbstract implements StatelessAccessorInterface
{
    /**
     * @var UserInterface
     */
    protected UserInterface $user;

    /**
     * @var SessionInterface
     */
    protected SessionInterface $session;

    /**
     * @var EventsManager
     */
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
    public function setSession(SessionInterface $session)
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

    /**
     * @param string $name
     * @param mixed  $source
     * @param mixed  $data
     *
     * @return mixed
     */
    public function fireEvent(string $name, $source, $data = null)
    {
        if (isset($this->eventsManager)) {
            return $this->eventsManager->fire($name, $source, $data);
        }

        return true;
    }
}
