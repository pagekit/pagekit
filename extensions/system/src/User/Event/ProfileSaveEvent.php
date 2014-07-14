<?php

namespace Pagekit\User\Event;

use Pagekit\Framework\Event\Event;
use Pagekit\User\Model\UserInterface;

class ProfileSaveEvent extends Event
{
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

    /**
     * @return array
     */
    public function getData ()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData ($data)
    {
        $this->data = $data;
    }

    /**
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }
}
