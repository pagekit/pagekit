<?php

namespace Pagekit\Kernel;

use Pagekit\Event\EventDispatcherInterface;
use Pagekit\Kernel\Event\ExceptionEvent;
use Pagekit\Kernel\Event\KernelEvent;
use Pagekit\Kernel\Event\ResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class HttpKernel
{
    const MASTER_REQUEST = 1;
    const SUB_REQUEST    = 2;

    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $events
     * @param RequestStack             $requestStack
     */
    public function __construct(EventDispatcherInterface $events, RequestStack $requestStack = null)
    {
        $this->events = $events;
        $this->requestStack = $requestStack ?: new RequestStack();
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        try {

            $event = new KernelEvent('kernel.request', $type);

            $this->requestStack->push($request);
            $this->events->trigger($event, [$request]);

            return $this->handleResponse($event, $request, $type);

        } catch (\Exception $exception) {

            return $this->handleException($exception, $request, $type);

        }
    }

    /**
     * Handles the response.
     *
     * @param  mixed $response
     * @param  Request  $request
     * @param  int      $type
     * @return Response
     *
     * @throws \RuntimeException
     */
    protected function handleResponse($e, $request, $type)
    {
        $event = new ResponseEvent('kernel.response', $type);
        $event->setResponse($e->getResponse());

        return $this->events->trigger($event, [$request])->getResponse();
    }

    /**
     * Handles an exception by trying to convert it to a Response.
     *
     * @param  \Exception $e
     * @param  Request    $request
     * @param  int        $type
     * @return Response
     *
     * @throws \Exception
     */
    protected function handleException(\Exception $e, $request, $type)
    {
        $event = new ExceptionEvent('kernel.exception', $type, $e);
        $this->events->trigger($event, [$request]);

        // a listener might have replaced the exception
        $e = $event->getException();

        if (!$event->hasResponse()) {
            // $this->finishRequest($request, $type);

            throw $e;
        }

        $response = $event->getResponse();

        // the developer asked for a specific status code
        if ($response->headers->has('X-Status-Code')) {

            $response->setStatusCode($response->headers->get('X-Status-Code'));
            $response->headers->remove('X-Status-Code');

        } elseif (!$response->isClientError() && !$response->isServerError() && !$response->isRedirect()) {
            // ensure that we actually have an error response
            if ($e instanceof HttpExceptionInterface) {
                // keep the HTTP status code and headers
                $response->setStatusCode($e->getStatusCode());
                $response->headers->add($e->getHeaders());
            } else {
                $response->setStatusCode(500);
            }
        }

        try {
            return $this->handleResponse($response, $request, $type);
        } catch (\Exception $e) {
            return $response;
        }
    }
}
