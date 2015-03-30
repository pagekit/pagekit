<?php

namespace Pagekit\Auth\Event;

use Pagekit\Auth\UserInterface;
use Symfony\Component\EventDispatcher\Event as BaseEvent;

class Event extends BaseEvent
{
    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * Constructor.
     *
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user = null)
    {
        $this->user = $user;
    }

    /**
     * Gets the user.
     *
     * @return UserInterface|null
     */
    public function getUser()
    {
        return $this->user;
    }
}
