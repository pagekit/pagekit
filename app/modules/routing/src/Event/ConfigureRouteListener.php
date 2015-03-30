<?php

namespace Pagekit\Routing\Event;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigureRouteListener implements EventSubscriberInterface
{
    protected $reader;
    protected $namespace;

    /**
     * Constructor.
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader = null)
    {
        $this->reader    = $reader;
        $this->namespace = 'Pagekit\Routing\Annotation';
    }

    /**
     * Reads the @Request and @Response annotations.
     *
     * @param ConfigureRouteEvent $event
     */
    public function onConfigureRoute(ConfigureRouteEvent $event)
    {
        $reader = $this->getReader();

        foreach (['_request' => 'Request', '_response' => 'Response'] as $name => $class) {

            $class = "{$this->namespace}\\$class";

            if (($annotation = $reader->getClassAnnotation($event->getClass(), $class) or $annotation = $reader->getMethodAnnotation($event->getMethod(), $class))
                and $data = $annotation->getData()
            ) {
                $event->getRoute()->setDefault($name, $data);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'route.configure' => 'onConfigureRoute'
        ];
    }

    /**
     * Gets an annotation reader.
     *
     * @return Reader
     */
    protected function getReader()
    {
        if (!$this->reader) {
            $this->reader = new SimpleAnnotationReader;
            $this->reader->addNamespace($this->namespace);
        }

        return $this->reader;
    }
}
