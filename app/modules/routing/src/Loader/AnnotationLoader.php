<?php

namespace Pagekit\Routing\Loader;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class AnnotationLoader implements LoaderInterface
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var int
     */
    protected $routeIndex;

    /**
     * @var string
     */
    protected $routeAnnotation = 'Pagekit\Routing\Annotation\Route';

    /**
     * Constructor.
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader = null)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function load($class)
    {
        if (class_exists($class)) {
            $class = new \ReflectionClass($class);
        } else {
            throw new \InvalidArgumentException(sprintf('Controller class "%s" does not exist.', $class));
        }

        if ($class->isAbstract()) {
            throw new \InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $class->getName()));
        }

        $routes = new RouteCollection();
        $globals = $this->getGlobals($class);

        foreach ($class->getMethods() as $method) {

            $this->routeIndex = 0;

            if ($method->isPublic() && 'Action' == substr($method->name, -6)) {

                $count = count($routes);

                foreach ($this->getAnnotationReader()->getMethodAnnotations($method) as $annotation) {
                    if ($annotation instanceof $this->routeAnnotation) {
                        $this->addRoute($routes, $class, $method, $annotation, $globals);
                    }
                }

                if ($count == count($routes)) {
                    $this->addRoute($routes, $class, $method, new $this->routeAnnotation(), $globals);
                }
            }
        }

        return $routes;
    }

    /**
     * Adds a new route.
     *
     * @param RouteCollection   $routes
     * @param \ReflectionClass  $class
     * @param \ReflectionMethod $method
     * @param RouteAnnotation   $annotation
     * @param array             $globals
     */
    protected function addRoute(RouteCollection $routes, \ReflectionClass $class, \ReflectionMethod $method, $annotation, $globals)
    {
        $name = $annotation->getName() ?: $this->getDefaultRouteName($class, $method);
        $path = $annotation->getPath() ?: $this->getDefaultRoutePath($class, $method);

        $route = new Route(rtrim($globals['path'].$path, '/'));
        $route->setDefaults(array_replace($globals['defaults'], $annotation->getDefaults(), ['_controller' => $class->name.'::'.$method->name]));
        $route->setRequirements(array_replace($globals['requirements'], $annotation->getRequirements()));
        $route->setOptions(array_replace($globals['options'], $annotation->getOptions()));
        $route->setHost($annotation->getHost() ?: $globals['host']);
        $route->setSchemes(array_replace($globals['schemes'], $annotation->getSchemes()));
        $route->setMethods(array_replace($globals['methods'], $annotation->getMethods()));
        $route->setCondition($annotation->getCondition() ?: $globals['condition']);

        $routes->add($globals['name'].$name, $route);
    }

    /**
     * @param  \ReflectionClass $class
     * @return array
     */
    protected function getGlobals(\ReflectionClass $class)
    {
        $globals = [
            'name'         => '',
            'path'         => '',
            'defaults'     => [],
            'requirements' => [],
            'options'      => [],
            'host'         => '',
            'schemes'      => [],
            'methods'      => [],
            'condition'    => ''
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

    /**
     * Get the annotation reader instance.
     *
     * @return Reader
     */
    protected function getAnnotationReader()
    {
        if (!$this->reader) {
            $this->reader = new SimpleAnnotationReader();
            $this->reader->addNamespace('Pagekit\Routing\Annotation');
        }

        return $this->reader;
    }

    /**
     * Gets the default route path for a class method.
     *
     * @param  \ReflectionClass  $class
     * @param  \ReflectionMethod $method
     * @return string
     */
    protected function getDefaultRoutePath(\ReflectionClass $class, \ReflectionMethod $method)
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
     * @param  \ReflectionClass  $class
     * @param  \ReflectionMethod $method
     * @return string
     */
    protected function getDefaultRouteName(\ReflectionClass $class, \ReflectionMethod $method)
    {
        if ('index' === $action = strtolower($this->parseControllerActionName($method))) {
            $action = '';
        }

        $name = "/{$action}";

        if ($this->routeIndex > 0) {
            $name .= "_{$this->routeIndex}";
        }

        $this->routeIndex++;

        return trim($name, '/');
    }

    /**
     * Parses the controller action name.
     *
     * @param  \ReflectionMethod $method
     * @throws \LogicException
     * @return string
     */
    protected function parseControllerActionName(\ReflectionMethod $method)
    {
        if (!preg_match('/([a-zA-Z0-9]+)Action$/', $method->name, $matches)) {
            throw new \LogicException(sprintf('Unable to retrieve action name. The controller class method %s does not follow the naming convention. (e.g. indexAction)', $method->name));
        }

        return $matches[1];
    }
}
