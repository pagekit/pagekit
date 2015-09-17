<?php

namespace Pagekit\Session;


use Symfony\Component\HttpFoundation\Session\Session as BaseSession;

class Session extends BaseSession
{
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
}
