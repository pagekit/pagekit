<?php

namespace Pagekit\Kernel;

use Pagekit\Event\EventDispatcherInterface;
use Pagekit\Kernel\Event\ControllerEvent;
use Pagekit\Kernel\Event\ExceptionEvent;
use Pagekit\Kernel\Event\KernelEvent;
use Pagekit\Kernel\Event\RequestEvent;
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

            $event = new RequestEvent('app.request', $type);

            $this->requestStack->push($request);
            $this->events->trigger($event, [$request]);

            if ($event->hasResponse()) {
                $response = $event->getResponse();
            } else {
                $response = $this->handleController($request, $type);
            }

            return $this->handleResponse($request, $response, $type);

        } catch (\Exception $exception) {

            return $this->handleException($exception, $request, $type);

        }
    }

    /**
     * Handles the controller event.
     *
     * @param  Request  $request
     * @param  int      $type
     * @return Response
     */
    protected function handleController(Request $request, $type)
    {
        $event = new ControllerEvent('app.controller', $type);
        $this->events->trigger($event, [$request]);

        $response = $event->getResponse();

        if (!$response instanceof Response) {

            $msg = 'The controller must return a response.';

            if ($response === null) {
                $msg .= ' Did you forget to add a return statement somewhere in your controller?';
            }

            throw new \LogicException($msg);
        }

        return $response;
    }

    /**
     * Handles the response event.
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $type
     * @return Response
     */
    protected function handleResponse(Request $request, Response $response, $type)
    {
        $event = new KernelEvent('app.response', $type, $response);
        $this->events->trigger($event, [$request, $response]);
        $this->requestStack->pop();

        return $response;
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
        $event = new ExceptionEvent('app.exception', $type, $e);
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
