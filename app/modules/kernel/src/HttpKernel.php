<?php

namespace Pagekit\Kernel;

use Pagekit\Event\EventDispatcherInterface;
use Pagekit\Kernel\Event\ControllerEvent;
use Pagekit\Kernel\Event\ExceptionEvent;
use Pagekit\Kernel\Event\KernelEvent;
use Pagekit\Kernel\Event\RequestEvent;
use Pagekit\Kernel\Exception\BadRequestException;
use Pagekit\Kernel\Exception\ConflictException;
use Pagekit\Kernel\Exception\ForbiddenException;
use Pagekit\Kernel\Exception\HttpException;
use Pagekit\Kernel\Exception\InternalErrorException;
use Pagekit\Kernel\Exception\MethodNotAllowedException;
use Pagekit\Kernel\Exception\NotFoundException;
use Pagekit\Kernel\Exception\UnauthorizedException;
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

            $event = new RequestEvent('request', $this);

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
     * {@inheritdoc}
     */
    public function abort($code, $message = null, array $headers = [])
    {
        switch ($code) {

            case 400:
                $exception = new BadRequestException($message);
                break;

            case 401:
                $exception = new UnauthorizedException($message);
                break;

            case 403:
                $exception = new ForbiddenException($message);
                break;

            case 404:
                $exception = new NotFoundException($message);
                break;

            case 405:
                $exception = new MethodNotAllowedException($message);
                break;

            case 409:
                $exception = new ConflictException($message);
                break;

            case 500:
                $exception = new InternalErrorException($message);
                break;

            default:
                $exception = new HttpException($message);
                break;
        }

        throw $exception;
    }

    /**
     * {@inheritdoc}
     */
    public function terminate(Request $request, Response $response)
    {
        $event = new KernelEvent('terminate', $this);
        $this->events->trigger($event, [$request, $response]);
    }

    /**
     * Handles the controller event.
     *
     * @return Response
     */
    protected function handleController()
    {
        $event = new ControllerEvent('controller', $this);
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
        $event = new KernelEvent('response', $this);
        $this->events->trigger($event, [$this->getRequest(), $response]);
        $this->stack->pop();

        return $response;
    }

    /**
     * Handles the exception event.
     *
     * @param  \Exception $e
     * @return Response
     * @throws \Exception
     */
    protected function handleException(\Exception $e)
    {
        $event = new ExceptionEvent('exception', $this, $e);
        $this->events->trigger($event, [$this->getRequest()]);

        $e = $event->getException();

        if (!$event->hasResponse()) {
            throw $e;
        }

        $response = $event->getResponse();

        if (!$response->isClientError() && !$response->isServerError() && !$response->isRedirect()) {
            if ($e instanceof HttpException) {
                $response->setStatusCode($e->getCode());
            } else {
                $response->setStatusCode(500);
            }
        }

        try {

            return $this->handleResponse($response);

        } catch (\Exception $e) {

            return $response;
        }
    }
}
