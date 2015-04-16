<?php

namespace Pagekit;

use Pagekit\Application\ExceptionListenerWrapper;
use Pagekit\Application\Traits\EventTrait;
use Pagekit\Application\Traits\StaticTrait;
use Pagekit\Event\EventDispatcher;
use Pagekit\Module\ModuleManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Application extends Container
{
    use StaticTrait, EventTrait;

    /**
     * Constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this['events'] = function() {
            return new EventDispatcher();
        };

        $this['module'] = function() {
            return new ModuleManager($this);
        };
    }

    /**
     * Aborts the current request by sending a proper HTTP error.
     *
     * @param  int    $code
     * @param  string $message
     * @param  array  $headers
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public static function abort($code, $message = '', array $headers = [])
    {
        self::router()->abort($code, $message, $headers);
    }

    /**
     * Registers an error handler.
     *
     * @param mixed   $callback
     * @param integer $priority
     */
    public static function error($callback, $priority = -8)
    {
        self::on('kernel.exception', new ExceptionListenerWrapper($callback), $priority);
    }

    /**
     * Handles the request and delivers the response.
     *
     * @param Request $request
     */
    public function run(Request $request = null)
    {
        if ($request === null) {
            $request = Request::createFromGlobals();
        }

        $response = $this['kernel']->handle($request);
        $response->send();

        $this['kernel']->terminate($request, $response);
    }

    /**
     * Determine if we are running in the console.
     *
     * @return bool
     */
    public function inConsole()
    {
        return PHP_SAPI == 'cli';
    }
}
