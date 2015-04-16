<?php

namespace Pagekit\Routing;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class Middleware
{
    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $events
     */
    public function __construct(EventDispatcherInterface $events)
    {
        $this->events = $events;

        $events->addListener(KernelEvents::REQUEST, function (GetResponseEvent $event) use ($events) {
            if ($name = $event->getRequest()->attributes->get('_route', '')) {
                $events->dispatch('before'.$name, $event);
            }
        });

        $events->addListener(KernelEvents::RESPONSE, function (FilterResponseEvent $event) use ($events) {
            if ($name = $event->getRequest()->attributes->get('_route', '')) {
                $events->dispatch('after'.$name, $event);
            }
        });
    }

    /**
     * Sets a callback to act before
     *
     * @param string   $name
     * @param callable $callback
     * @param int      $priority
     */
    public function before($name, $callback, $priority)
    {
        $this->events->addListener('before'.$name, $callback, $priority);
    }

    /**
     * @param string   $name
     * @param callable $callback
     * @param int      $priority
     */
    public function after($name, $callback, $priority)
    {
        $this->events->addListener('after'.$name, $callback, $priority);
    }
}
