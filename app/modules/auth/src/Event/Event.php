<?php

namespace Pagekit\Auth\Event;

use Pagekit\Auth\UserInterface;
use Pagekit\Event\Event as BaseEvent;

class Event extends BaseEvent
{
    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * Constructor.
     *
     * @param string $name	 
     * @param UserInterface $user
     */
    public function __construct($name, UserInterface $user = null)
    {
        parent::__construct($name);

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
