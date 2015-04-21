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

class HttpKernel implements HttpKernelInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * @var RequestStack
     */
    protected $stack;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $events
     * @param RequestStack             $stack
     */
    public function __construct(EventDispatcherInterface $events, RequestStack $stack = null)
    {
        $this->events = $events;
        $this->stack  = $stack ?: new RequestStack();
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->stack->getCurrentRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function isMasterRequest()
    {
        return $this->getRequest() === $this->stack->getMasterRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request)
    {
        try {

            $event = new RequestEvent('app.request', $this);

            $this->stack->push($request);
            $this->events->trigger($event, [$request]);

            if ($event->hasResponse()) {
                $response = $event->getResponse();
            } else {
                $response = $this->handleController();
            }

            return $this->handleResponse($response);

        } catch (\Exception $e) {

            return $this->handleException($e);

        }
    }

    /**
     * Handles the controller event.
     *
     * @return Response
     */
    protected function handleController()
    {
        $event = new ControllerEvent('app.controller', $this);
        $this->events->trigger($event, [$this->getRequest()]);

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
     * @param  Response $response
     * @return Response
     */
    protected function handleResponse(Response $response)
    {
        $event = new KernelEvent('app.response', $this);
        $this->events->trigger($event, [$this->getRequest(), $response]);
        $this->stack->pop();

        return $response;
    }

    /**
     * Handles an exception by trying to convert it to a Response.
     *
     * @param  \Exception $e
     * @return Response
     */
    protected function handleException(\Exception $e)
    {
        $event = new ExceptionEvent('app.exception', $this, $e);
        $this->events->trigger($event, [$this->getRequest()]);

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
