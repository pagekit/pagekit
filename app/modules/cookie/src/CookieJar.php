<?php

namespace Pagekit\Cookie;

use Symfony\Component\HttpFoundation\Cookie;

class CookieJar
{
    /**
     * The default path.
     *
     * @var string
     */
    protected $path = '/';

    /**
     * The default domain.
     *
     * @var string
     */
    protected $domain;

    /**
     * @var Cookie[]
     */
    protected $cookies = [];

    /**
     * Sets the default path and domain.
     *
     * @param string $path
     * @param null   $domain
     */
    public function setDefaultPathAndDomain($path = '/', $domain = null)
    {
        $this->path = $path;
        $this->domain = $domain;
    }

    /**
     * Determines if a cookie has been queued.
     *
     * @param  string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->cookies[$name]);
    }

    /**
     * Get a queued cookie instance.
     *
     * @param  string $name
     * @return Cookie
     */
    public function get($name)
    {
        return isset($this->cookies[$name]) ? $this->cookies[$name] : null;
    }

    /**
     * Creates a new cookie instance.
     *
     * @param  string $name
     * @param  string $value
     * @param  int    $expire
     * @param  string $path
     * @param  string $domain
     * @param  bool   $secure
     * @param  bool   $httpOnly
     * @return Cookie
     */
    public function set($name, $value, $expire = 0, $path = null, $domain = null, $secure = false, $httpOnly = true)
    {
        if (null === $path) {
            $path = $this->path;
        }

        if (null === $domain) {
            $domain = $this->domain;
        }

        return $this->cookies[$name] = new Cookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Expires the given cookie.
     *
     * @param  string $name
     * @param  string $path
     * @param  string $domain
     * @return Cookie
     */
    public function remove($name, $path = null, $domain = null)
    {
        if (null === $path) {
            $path = $this->path;
        }

        if (null === $domain) {
            $domain = $this->domain;
        }

        return $this->set($name, null, 1, $path, $domain);
    }

    /**
     * Returns queued cookies to set on response.
     *
     * @return Cookie[]
     */
    public function getQueuedCookies()
    {
        return $this->cookies;
    }
}
