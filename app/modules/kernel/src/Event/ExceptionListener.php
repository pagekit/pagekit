<?php

namespace Pagekit\Kernel\Event;

use Pagekit\Event\EventSubscriberInterface;
use Pagekit\Kernel\Exception\HttpException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;

class ExceptionListener implements EventSubscriberInterface
{
    protected $controller;
    protected $logger;

    public function __construct($controller, LoggerInterface $logger = null)
    {
        $this->controller = $controller;
        $this->logger = $logger;
    }

    public function onException($event, $request)
    {
        static $handling;

        if ($handling === true) {
            return false;
        }

        $handling = true;

        $exception = $event->getException();

        $this->logException($exception, sprintf('Uncaught PHP Exception %s: "%s" at %s line %s', get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine()));

        $request = $this->duplicateRequest($exception, $request);

        try {

            $response = $event->getKernel()->handle($request);

        } catch (\Exception $e) {

            $this->logException($e, sprintf('Exception thrown when handling an exception (%s: %s at %s line %s)', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));

            $handling = false;
            $wrapper  = $e;

            while ($prev = $wrapper->getPrevious()) {
                if ($exception === $wrapper = $prev) {
                    throw $e;
                }
            }

            $prev = new \ReflectionProperty('Exception', 'previous');
            $prev->setAccessible(true);
            $prev->setValue($wrapper, $exception);

            throw $e;
        }

        $event->setResponse($response);

        $handling = false;
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'exception' => ['onException', -100]
        ];
    }

    /**
     * Logs an exception.
     *
     * @param \Exception $exception
     * @param string     $message
     */
    protected function logException(\Exception $exception, $message)
    {
        if ($this->logger !== null) {
            if (!$exception instanceof HttpException || $exception->getCode() >= 500) {
                $this->logger->critical($message, ['exception' => $exception]);
            } else {
                $this->logger->error($message, ['exception' => $exception]);
            }
        }
    }

    /**
     * Clones the request for the exception.
     *
     * @param \Exception $exception
     * @param  Request   $request
     * @return Request   $request
     */
    protected function duplicateRequest(\Exception $exception, Request $request)
    {
        $attributes = [
            '_controller' => $this->controller,
            'exception'   => FlattenException::create($exception),
            'logger'      => $this->logger
        ];

        $request = $request->duplicate(null, null, $attributes);
        $request->setMethod('GET');

        return $request;
    }
}
