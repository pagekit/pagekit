<?php

namespace Pagekit\Templating\Section;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class DelayedRenderer
{
    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    public function __construct(EventDispatcherInterface $events)
    {
        $this->events = $events;
    }

    protected function render($value)
    {
        if (is_array($value)) {
            return implode(PHP_EOL, array_map([$this, 'render'], $value));
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        if (is_callable($value)) {
            return $value();
        }
    }

    public function __invoke($name, $value, $options)
    {
        $placeholder = '<!-- '.uniqid('section.').' -->';

        $this->events->addListener(KernelEvents::RESPONSE, function(FilterResponseEvent $event) use ($value, $placeholder) {

            $response = $event->getResponse();
            $response->setContent(str_replace($placeholder, $this->render($value), $response->getContent()));

        }, 10);

        return $placeholder;
    }
}
