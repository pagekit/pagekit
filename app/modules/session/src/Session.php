<?php

namespace Pagekit\Session;

use Symfony\Component\HttpFoundation\Session\Session as BaseSession;

class Session extends BaseSession
{
    /**
     * @var int
     */
    protected $lastActive;

    /**
     * {@inheritdoc}
     */
    public function __construct($storage = null, $attributes = null, $flashes = null)
    {
        parent::__construct($storage, $attributes, $flashes);

        $this->lastActive = (int) $this->get($this->getKey('lastActive'));
        $this->set($this->getKey('lastActive'), time());
    }

    /**
     * Get a unique identifier for the auth session value.
     *
     * @param  string $var
     * @return string
     */
    public function getKey($var)
    {
        return "_session.{$var}_" . sha1(get_class($this));
    }

    /**
     * @return int
     */
    public function getLifetime()
    {
        return (int) ini_get('session.gc_maxlifetime');
    }

    /**
     * @param int $lifetime
     */
    public function setLifetime($lifetime)
    {
        ini_set('session.gc_maxlifetime', $lifetime);
    }

    public function getLastActive()
    {
        return $this->lastActive;
    }
}
