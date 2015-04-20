<?php

namespace Pagekit\Routing\Event;

use Pagekit\Event\EventSubscriberInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RequestContextAwareInterface;
use Symfony\Component\HttpFoundation\Request;

class RouterListener implements EventSubscriberInterface
{
    protected $matcher;
    protected $logger;

    /**
     * Constructor.
     *
     * @param  UrlMatcherInterface|RequestMatcherInterface $matcher
     * @param  LoggerInterface|null                        $logger
     * @throws \InvalidArgumentException
     */
    public function __construct($matcher, LoggerInterface $logger = null)
    {
        if (!$matcher instanceof UrlMatcherInterface && !$matcher instanceof RequestMatcherInterface) {
            throw new \InvalidArgumentException('Matcher must either implement UrlMatcherInterface or RequestMatcherInterface.');
        }

        $this->matcher = $matcher;
        $this->logger  = $logger;
    }

    public function onRequest($event, $request)
    {
        if ($request->attributes->has('_controller')) {
            return;
        }

        try {

            if ($this->matcher instanceof RequestMatcherInterface) {
                $parameters = $this->matcher->matchRequest($request);
            } else {
                $parameters = $this->matcher->match($request->getPathInfo());
            }

            if (null !== $this->logger) {
                $this->logger->info(sprintf('Matched route "%s" (parameters: %s)', $parameters['_route'], $this->parametersToString($parameters)));
            }

            $request->attributes->add($parameters);
            unset($parameters['_route'], $parameters['_controller']);
            $request->attributes->set('_route_params', $parameters);

        } catch (ResourceNotFoundException $e) {

            $message = sprintf('No route found for "%s %s"', $request->getMethod(), $request->getPathInfo());

            if ($referer = $request->headers->get('referer')) {
                $message .= sprintf(' (from "%s")', $referer);
            }

            throw new NotFoundHttpException($message, $e);

        } catch (MethodNotAllowedException $e) {

            $message = sprintf('No route found for "%s %s": Method Not Allowed (Allow: %s)', $request->getMethod(), $request->getPathInfo(), implode(', ', $e->getAllowedMethods()));

            throw new MethodNotAllowedHttpException($e->getAllowedMethods(), $message, $e);
        }
    }

    public function parametersToString(array $parameters)
    {
        $pieces = [];

        foreach ($parameters as $key => $val) {
            $pieces[] = sprintf('"%s": "%s"', $key, (is_string($val) ? $val : json_encode($val)));
        }

        return implode(', ', $pieces);
    }

    public function subscribe()
    {
        return array(
            'app.request' => ['onRequest', 100]
        );
    }
}
