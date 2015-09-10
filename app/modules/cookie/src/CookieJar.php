<?php

namespace Pagekit\Cookie;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

class CookieJar
{
    /*
     * The current request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * The default path.
     *
     * @var string
     */
    protected $path;

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
     * Constructor.
     *
     * @param  Request $request
     * @param  string  $path
     * @param  string  $domain
     */
    public function __construct(Request $request, $path = '/', $domain = null)
    {
        $this->request = $request;
        $this->path    = $path;
        $this->domain  = $domain;
    }


    /**
     * Determine if a cookie exists and is not null.
     *
     * @param  string $key
     * @return bool
     */
    public function has($key)
    {
        return $this->request->cookies->has($key);
    }

    /**
     * Get the value of the given cookie.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->request->cookies->get($key, $default);
    }

    /**
     * Create a new cookie instance.
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

        return $this->cookies[] = new Cookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Expire the given cookie.
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
     * Return queued cookies to set on response.
     *
     * @return Cookie[]
     */
    public function getQueuedCookies()
    {
        return $this->cookies;
    }
}
