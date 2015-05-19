<?php

namespace Pagekit\Routing\Controller;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Pagekit\Event\EventDispatcherInterface;
use Pagekit\Routing\Annotation\Route as RouteAnnotation;
use Pagekit\Routing\Event\ConfigureRouteEvent;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ControllerReader implements ControllerReaderInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @var int
     */
    protected $routeIndex;

    /**
     * @var string
     */
    protected $routeAnnotation = 'Pagekit\Routing\Annotation\Route';

    /**
     * @var \ReflectionClass
     */
    protected $route;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $events
     * @param Reader                   $reader
     */
    public function __construct(EventDispatcherInterface $events, Reader $reader = null)
    {
        $this->events = $events;
        $this->reader = $reader;
        $this->route  = new ReflectionClass('Symfony\Component\Routing\Route');
    }

    /**
     * {@inheritdoc}
     */
    public function read($class)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Controller class "%s" does not exist.', $class));
        }

        $class = new \ReflectionClass($class);

        if ($class->isAbstract()) {
            throw new \InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $class));
        }

        $globals = $this->getGlobals($class);

        $this->routes = new RouteCollection;

        foreach ($class->getMethods() as $method) {

            $this->routeIndex = 0;

            if ($method->isPublic() && 'Action' == substr($method->name, -6)) {

                $count = $this->routes->count();

                foreach ($this->getAnnotationReader()->getMethodAnnotations($method) as $annotation) {
                    if ($annotation instanceof $this->routeAnnotation) {
                        $this->addRoute($class, $method, $globals, $annotation);
                    }
                }

                if ($count == $this->routes->count()) {
                    $this->addRoute($class, $method, $globals, new $this->routeAnnotation([]));
                }
            }
        }

        return $this->routes;
    }

    /**
     * Get the annotation reader instance.
     *
     * @return Reader
     */
    protected function getAnnotationReader()
    {
        if (!$this->reader) {
            $this->reader = new SimpleAnnotationReader;
            $this->reader->addNamespace('Pagekit\Routing\Annotation');
        }

        return $this->reader;
    }

    /**
     * Creates a new route.
     *
     * @param ReflectionClass  $class
     * @param ReflectionMethod $method
     * @param array            $globals
     * @param RouteAnnotation  $annotation
     */
    protected function addRoute(ReflectionClass $class, ReflectionMethod $method, array $globals, $annotation)
    {
        $options = [
            'path'         => rtrim($globals['path'].(null !== $annotation->getPath() ? $annotation->getPath() : $this->getDefaultRoutePath($class, $method, $globals)), '/'),
            'defaults'     => array_merge($globals['defaults'], $annotation->getDefaults()),
            'requirements' => array_merge($globals['requirements'], $annotation->getRequirements()),
            'options'      => array_merge($globals['options'], $annotation->getOptions()),
            'host'         => null !== $annotation->getHost() ? $annotation->getHost() : $globals['host'],
            'schemes'      => array_merge($globals['schemes'], $annotation->getSchemes()),
            'methods'      => array_merge($globals['methods'], $annotation->getMethods()),
            'condition'    => null !== $annotation->getCondition() ? $annotation->getCondition() : $globals['condition'],
            'name'         => $annotation->getName() ?: $this->getDefaultRouteName($class, $method, $globals)
        ];

        if ($route = $this->configureRoute($this->route->newInstanceArgs($options), $class, $method, $options)) {
            $this->routes->add($options['name'], $route);
        }
    }

    /**
     * Configure the route, should be overridden in subclasses.
     *
     * @param  Route            $route
     * @param  ReflectionClass  $class
     * @param  ReflectionMethod $method
     * @param  array            $options
     * @return Route|null
     */
    protected function configureRoute(Route $route, ReflectionClass $class, ReflectionMethod $method, array $options)
    {
        $route->setDefault('_controller', $class->name.'::'.$method->name);
        return $this->events->trigger(new ConfigureRouteEvent($route, $class, $method, $options))->getRoute();
    }

    /**
     * Gets the default route path for a class method.
     *
     * @param  ReflectionClass  $class
     * @param  ReflectionMethod $method
     * @param  array            $globals
     * @return string
     */
    protected function getDefaultRoutePath(ReflectionClass $class, ReflectionMethod $method, array $globals)
    {
        $action = strtolower('/'.$this->parseControllerActionName($method));

        if ($action == '/index') {
            $action = '';
        }

        return $action;
    }

    /**
     * Gets the default route name for a class method.
     *
     * @param  ReflectionClass  $class
     * @param  ReflectionMethod $method
     * @param  array            $globals
     * @return string
     */
    protected function getDefaultRouteName(ReflectionClass $class, ReflectionMethod $method, array $globals)
    {
        if ('index' === $action = strtolower($this->parseControllerActionName($method))) {
            $action = '';
        }

        $name = $globals['name'].'/'.$action;

        if ($this->routeIndex > 0) {
            $name .= '_'.$this->routeIndex;
        }

        $this->routeIndex++;

        return trim($name, '/');
    }

    /**
     * Parses the controller action name.
     *
     * @param  ReflectionMethod $method
     * @throws \LogicException
     * @return string
     */
    protected function parseControllerActionName(ReflectionMethod $method)
    {
        if (!preg_match('/([a-zA-Z0-9]+)Action$/', $method->name, $matches)) {
            throw new \LogicException(sprintf('Unable to retrieve action name. The controller class method %s does not follow the naming convention. (e.g. indexAction)', $method->name));
        }

        return $matches[1];
    }

    /**
     * @param  ReflectionClass $class
     * @return array
     */
    protected function getGlobals(ReflectionClass $class)
    {
        $globals = [
            'path'         => null,
            'defaults'     => [],
            'requirements' => [],
            'options'      => [],
            'host'         => '',
            'schemes'      => [],
            'methods'      => [],
            'condition'    => '',
            'name'         => null
        ];

        if ($annotation = $this->getAnnotationReader()->getClassAnnotation($class, $this->routeAnnotation)) {
            foreach (array_keys($globals) as $option) {
                $method = 'get'.ucfirst($option);
                if (null !== $value = $annotation->$method()) {
                    $globals[$option] = $value;
                }
            }
        }

        return $globals;
    }
}
