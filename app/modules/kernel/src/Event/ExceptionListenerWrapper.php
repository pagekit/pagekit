<?php

namespace Pagekit\Kernel\Event;

use Pagekit\Kernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;

class ExceptionListenerWrapper
{
    protected $callback;

    /**
     * Constructor.
     *
     * @param mixed $callback
     */
    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function __invoke($event)
    {
        $exception = $event->getException();

        if (!$this->shouldRun($exception)) {
            return;
        }

        $code = $exception instanceof HttpException ? $exception->getCode() : 500;

        $response = call_user_func($this->callback, $exception, $code);

        if ($response instanceof Response) {
            $event->setResponse($response);
        }
    }

    protected function shouldRun(\Exception $exception)
    {
        if (is_array($this->callback)) {
            $callbackReflection = new \ReflectionMethod($this->callback[0], $this->callback[1]);
        } elseif (is_object($this->callback) && !$this->callback instanceof \Closure) {
            $callbackReflection = new \ReflectionObject($this->callback);
            $callbackReflection = $callbackReflection->getMethod('__invoke');
        } else {
            $callbackReflection = new \ReflectionFunction($this->callback);
        }

        if ($callbackReflection->getNumberOfParameters() > 0) {
            $parameters = $callbackReflection->getParameters();
            $expectedException = $parameters[0];
            if ($expectedException->getClass() && !$expectedException->getClass()->isInstance($exception)) {
                return false;
            }
        }

        return true;
    }
}
