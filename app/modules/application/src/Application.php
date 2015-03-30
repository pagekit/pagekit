<?php

namespace Pagekit;

use Pagekit\Application\ExceptionListenerWrapper;
use Pagekit\Application\Traits\EventTrait;
use Pagekit\Application\Traits\StaticTrait;
use Pagekit\Event\EventDispatcher;
use Pagekit\Module\ModuleManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\TerminableInterface;

class Application extends Container implements HttpKernelInterface, TerminableInterface
{
    use StaticTrait, EventTrait;

    /**
     * @var boolean
     */
    protected $booted = false;

    /**
     * Constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this['events'] = function() {
            return new EventDispatcher;
        };

        $this['module'] = function() {
            return new ModuleManager($this);
        };
    }

    /**
     * Boots all service providers.
     *
     * This method is automatically called by handle(), but you can use it
     * to boot all service providers when not handling a request.
     */
    public function boot()
    {
        if ($this->booted) {
            return;
        }

        $this['events']->dispatch('kernel.boot');
        $this->booted = true;
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
        self::on(KernelEvents::EXCEPTION, new ExceptionListenerWrapper(static::getInstance(), $callback), $priority);
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

        $response = $this->handle($request);
        $response->send();

        $this->terminate($request, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        if (!$this->booted) {
            $this->boot();
        }

        $this['request'] = $request;

        return $this['router']->handle($request, $type, $catch);
    }

    /**
     * {@inheritdoc}
     */
    public function terminate(Request $request, Response $response)
    {
        $this['router']->terminate($request, $response);
    }

    /**
     * Determine if we are running in the console.
     *
     * @return bool
     */
    public function runningInConsole()
    {
        return PHP_SAPI == 'cli';
    }
}
