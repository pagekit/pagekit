<?php

namespace Pagekit\Application\Traits;

use Pagekit\Kernel\Event\ExceptionListenerWrapper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait RouterTrait
{
    /**
     * @see HttpKernel::abort()
     */
    public static function abort($code, $message = null, array $headers = [])
    {
        static::kernel()->abort($code, $message, $headers);
    }

    /**
     * Registers an error handler.
     *
     * @param mixed   $callback
     * @param integer $priority
     */
    public static function error($callback, $priority = -8)
    {
        static::events()->on('exception', new ExceptionListenerWrapper($callback), $priority);
    }

    /**
     * @see Router::redirect()
     */
    public static function redirect($url = '', $parameters = [], $status = 302, $headers = [])
    {
        return static::router()->redirect($url, $parameters, $status, $headers);
    }

    /**
     * Handles a subrequest to forward an action internally.
     *
     * @param  string $name
     * @param  array  $parameters
     * @throws \RuntimeException
     * @return Response
     */
    public static function forward($name, $parameters = [])
    {
        if (!$request = static::request()) {
            throw new \RuntimeException('No Request set.');
        }

        return static::kernel()->handle(
            Request::create(
                static::router()->generate($name, $parameters), 'GET', [],
                $request->cookies->all(), [],
                $request->server->all()
            ));
    }
}
