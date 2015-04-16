<?php

namespace Pagekit\Kernel\Event;

use Pagekit\Event\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @copyright Copyright (c) 2004-2015 Fabien Potencier
 */
class ProfilerListener implements EventSubscriberInterface
{
    protected $profiler;
    protected $matcher;
    protected $onlyException;
    protected $onlyMasterRequests;
    protected $exception;
    protected $requests = array();
    protected $profiles;
    protected $requestStack;
    protected $parents;

    /**
     * Constructor.
     *
     * @param Profiler                     $profiler           A Profiler instance
     * @param RequestMatcherInterface|null $matcher            A RequestMatcher instance
     * @param bool                         $onlyException      true if the profiler only collects data when an exception occurs, false otherwise
     * @param bool                         $onlyMasterRequests true if the profiler only collects data when the request is a master request, false otherwise
     * @param RequestStack|null            $requestStack       A RequestStack instance
     */
    public function __construct(Profiler $profiler, RequestMatcherInterface $matcher = null, $onlyException = false, $onlyMasterRequests = false, RequestStack $requestStack = null)
    {
        $this->profiler = $profiler;
        $this->matcher = $matcher;
        $this->onlyException = (bool) $onlyException;
        $this->onlyMasterRequests = (bool) $onlyMasterRequests;
        $this->profiles = new \SplObjectStorage();
        $this->parents = new \SplObjectStorage();
        $this->requestStack = $requestStack;
    }

    /**
     * Handles the onKernelException event.
     *
     * @param GetResponseForExceptionEvent $event A GetResponseForExceptionEvent instance
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($this->onlyMasterRequests && !$event->isMasterRequest()) {
            return;
        }

        $this->exception = $event->getException();
    }

    /**
     * @deprecated Deprecated since version 2.4, to be removed in 3.0.
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (null === $this->requestStack) {
            $this->requests[] = $event->getRequest();
        }
    }

    /**
     * Handles the onKernelResponse event.
     *
     * @param FilterResponseEvent $event A FilterResponseEvent instance
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $master = $event->isMasterRequest();
        if ($this->onlyMasterRequests && !$master) {
            return;
        }

        if ($this->onlyException && null === $this->exception) {
            return;
        }

        $request = $event->getRequest();
        $exception = $this->exception;
        $this->exception = null;

        if (null !== $this->matcher && !$this->matcher->matches($request)) {
            return;
        }

        if (!$profile = $this->profiler->collect($request, $event->getResponse(), $exception)) {
            return;
        }

        $this->profiles[$request] = $profile;

        if (null !== $this->requestStack) {
            $this->parents[$request] = $this->requestStack->getParentRequest();
        } elseif (!$master) {
            // to be removed when requestStack is required
            array_pop($this->requests);

            $this->parents[$request] = end($this->requests);
        }
    }

    public function onKernelTerminate(PostResponseEvent $event)
    {
        // attach children to parents
        foreach ($this->profiles as $request) {
            // isset call should be removed when requestStack is required
            if (isset($this->parents[$request]) && null !== $parentRequest = $this->parents[$request]) {
                if (isset($this->profiles[$parentRequest])) {
                    $this->profiles[$parentRequest]->addChild($this->profiles[$request]);
                }
            }
        }

        // save profiles
        foreach ($this->profiles as $request) {
            $this->profiler->saveProfile($this->profiles[$request]);
        }

        $this->profiles = new \SplObjectStorage();
        $this->parents = new \SplObjectStorage();
        $this->requests = array();
    }

    public function subscribe()
    {
        return array(
            // kernel.request must be registered as early as possible to not break
            // when an exception is thrown in any other kernel.request listener
            'kernel.request'   => array('onKernelRequest', 1024),
            'kernel.response'  => array('onKernelResponse', -100),
            'kernel.exception' => 'onKernelException',
            'kernel.terminate' => array('onKernelTerminate', -1024),
        );
    }
}
