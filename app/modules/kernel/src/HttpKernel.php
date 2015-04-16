<?php

namespace Pagekit\Kernel;

use Pagekit\Event\EventDispatcherInterface;
use Pagekit\Kernel\Event\FilterControllerEvent;
use Pagekit\Kernel\Event\FilterResponseEvent;
use Pagekit\Kernel\Event\FinishRequestEvent;
use Pagekit\Kernel\Event\GetResponseEvent;
use Pagekit\Kernel\Event\GetResponseForControllerResultEvent;
use Pagekit\Kernel\Event\GetResponseForExceptionEvent;
use Pagekit\Kernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @copyright Copyright (c) 2004-2015 Fabien Potencier
 */
class HttpKernel implements HttpKernelInterface, TerminableInterface
{
    protected $events;
    protected $resolver;
    protected $requestStack;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface    $events
     * @param ControllerResolverInterface $resolver
     * @param RequestStack                $requestStack
     */
    public function __construct(EventDispatcherInterface $events, ControllerResolverInterface $resolver, RequestStack $requestStack = null)
    {
        $this->events = $events;
        $this->resolver = $resolver;
        $this->requestStack = $requestStack ?: new RequestStack();
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        if ($type == HttpKernelInterface::MASTER_REQUEST) {
            $this->events->trigger('kernel.boot');
        }

        try {

            return $this->handleRaw($request, $type);

        } catch (\Exception $e) {

            if (false === $catch) {
                $this->finishRequest($request, $type);

                throw $e;
            }

            return $this->handleException($e, $request, $type);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function terminate(Request $request, Response $response)
    {
        $this->events->trigger(new PostResponseEvent($this, $request, $response), [$request, $response]);
    }

    /**
     * @throws \LogicException If the request stack is empty
     * @internal
     */
    public function terminateWithException(\Exception $exception)
    {
        if (!$request = $this->requestStack->getMasterRequest()) {
            throw new \LogicException('Request stack is empty', 0, $exception);
        }

        $response = $this->handleException($exception, $request, self::MASTER_REQUEST);
        $response->sendHeaders();
        $response->sendContent();

        $this->terminate($request, $response);
    }

    /**
     * Handles a request to convert it to a response.
     *
     * Exceptions are not caught.
     *
     * @param  Request  $request
     * @param  int      $type
     * @return Response
     *
     * @throws \LogicException
     * @throws NotFoundHttpException
     */
    private function handleRaw(Request $request, $type = self::MASTER_REQUEST)
    {
        $this->requestStack->push($request);

        // request
        $event = new GetResponseEvent('kernel.request', $this, $request, $type);
        $this->events->trigger($event, [$request]);

        if ($event->hasResponse()) {
            return $this->filterResponse($event->getResponse(), $request, $type);
        }

        // load controller
        if (false === $controller = $this->resolver->getController($request)) {
            throw new NotFoundHttpException(sprintf('Unable to find the controller for path "%s". The route is wrongly configured.', $request->getPathInfo()));
        }

        $event = new FilterControllerEvent('kernel.controller', $this, $controller, $request, $type);
        $this->events->trigger($event, [$request]);
        $controller = $event->getController();

        // controller arguments
        $arguments = $this->resolver->getArguments($request, $controller);

        // call controller
        $response = call_user_func_array($controller, $arguments);

        // view
        if (!$response instanceof Response) {

            $event = new GetResponseForControllerResultEvent('kernel.view', $this, $request, $type, $response);
            $this->events->trigger($event, [$request]);

            if ($event->hasResponse()) {
                $response = $event->getResponse();
            }

            if (!$response instanceof Response) {

                $msg = sprintf('The controller must return a response (%s given).', $this->varToString($response));

                // the user may have forgotten to return something
                if (null === $response) {
                    $msg .= ' Did you forget to add a return statement somewhere in your controller?';
                }

                throw new \LogicException($msg);
            }
        }

        return $this->filterResponse($response, $request, $type);
    }

    /**
     * Filters a response object.
     *
     * @param  Response $response
     * @param  Request  $request
     * @param  int      $type
     * @return Response
     *
     * @throws \RuntimeException
     */
    private function filterResponse(Response $response, Request $request, $type)
    {
        $event = new FilterResponseEvent('kernel.response', $this, $request, $type, $response);
        $this->events->trigger($event, [$request, $response]);

        $this->finishRequest($request, $type);

        return $event->getResponse();
    }

    /**
     * Publishes the finish request event, then pop the request from the stack.
     *
     * @param Request $request
     * @param int     $type
     */
    private function finishRequest(Request $request, $type)
    {
        $this->events->trigger(new FinishRequestEvent('kernel.finish_request', $this, $request, $type), [$request]);
        $this->requestStack->pop();
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
    private function handleException(\Exception $e, $request, $type)
    {
        $event = new GetResponseForExceptionEvent('kernel.exception', $this, $request, $type, $e);
        $this->events->trigger($event, [$request]);

        // a listener might have replaced the exception
        $e = $event->getException();

        if (!$event->hasResponse()) {
            $this->finishRequest($request, $type);

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
            return $this->filterResponse($response, $request, $type);
        } catch (\Exception $e) {
            return $response;
        }
    }

    private function varToString($var)
    {
        if (is_object($var)) {
            return sprintf('Object(%s)', get_class($var));
        }

        if (is_array($var)) {
            $a = array();
            foreach ($var as $k => $v) {
                $a[] = sprintf('%s => %s', $k, $this->varToString($v));
            }

            return sprintf('Array(%s)', implode(', ', $a));
        }

        if (is_resource($var)) {
            return sprintf('Resource(%s)', get_resource_type($var));
        }

        if (null === $var) {
            return 'null';
        }

        if (false === $var) {
            return 'false';
        }

        if (true === $var) {
            return 'true';
        }

        return (string) $var;
    }
}
