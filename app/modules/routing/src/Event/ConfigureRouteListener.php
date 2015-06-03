<?php

namespace Pagekit\Routing\Event;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Pagekit\Event\EventSubscriberInterface;

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
     * Reads the @Request annotations.
     */
    public function onConfigureRoute($event, $route)
    {
        if (!$route->getControllerClass()) {
            return;
        }

        $reader = $this->getReader();

        foreach (['_request' => 'Request'] as $name => $class) {

            $class = "{$this->namespace}\\$class";

            if (($annotation = $reader->getClassAnnotation($route->getControllerClass(), $class) or $annotation = $reader->getMethodAnnotation($route->getControllerMethod(), $class))
                and $data = $annotation->getData()
            ) {
                $route->setDefault($name, $data);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
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
