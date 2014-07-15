<?php

namespace Pagekit\User\Event;

use Pagekit\Framework\Event\Event;
use Pagekit\System\Entity\DataTrait;
use Pagekit\User\Model\UserInterface;

class ProfileSaveEvent extends Event
{
    use DataTrait;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var array
     */
    protected $data;

    /**
     * @param UserInterface $user
     * @param array         $data
     */
    public function __construct(UserInterface $user, $data = [])
    {
        $this->user = $user;
        $this->data = $data;
    }

    /**
     * @return UserInterface
     */
    public function getUser ()
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser ($user)
    {
        $this->user = $user;
    }
}
