<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Event\Object;

use TimurFlush\Auth\Checker\CheckerInterface;
use TimurFlush\Auth\Event\EventInterface;
use TimurFlush\Auth\Session\SessionInterface;
use TimurFlush\Auth\User\UserInterface;

class FailedChecker implements EventInterface
{
    /**
     * @var CheckerInterface
     */
    protected CheckerInterface $checker;

    /**
     * @var UserInterface
     */
    protected UserInterface $user;

    /**
     * @var SessionInterface|null
     */
    protected ?SessionInterface $session;

    /**
     * Logout constructor.
     *
     * @param CheckerInterface      $checker
     * @param UserInterface         $user
     * @param SessionInterface|null $session
     */
    public function __construct(
        CheckerInterface $checker,
        UserInterface $user,
        ?SessionInterface $session = null
    ) {
        $this->checker = $checker;
        $this->user = $user;
        $this->session = $session;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'failedChecker';
    }

    /**
     * Returns a checker which associated with this event.
     *
     * @return CheckerInterface
     */
    public function getChecker(): CheckerInterface
    {
        return $this->checker;
    }

    /**
     * Returns a user which associated with this event.
     *
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * Returns a session which associated with this event.
     *
     * @return SessionInterface|null
     */
    public function getSession(): ?SessionInterface
    {
        return isset($this->session)
            ? $this->session
            : null;
    }
}
