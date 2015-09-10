<?php

namespace Pagekit\Routing\Loader;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Pagekit\Routing\Annotation\Route as RouteAnnotation;
use Pagekit\Routing\Route;

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

        $routes = [];
        $globals = $this->getGlobals($class);

        foreach ($class->getMethods() as $method) {

            $this->routeIndex = 0;

            if ($method->isPublic() && 'Action' == substr($method->name, -6)) {

                $count = count($routes);

                foreach ($this->getAnnotationReader()->getMethodAnnotations($method) as $annotation) {
                    if (is_a($annotation, $this->routeAnnotation, true)) {
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
     * @param Route[]           $routes
     * @param \ReflectionClass  $class
     * @param \ReflectionMethod $method
     * @param RouteAnnotation   $annotation
     * @param array             $globals
     */
    protected function addRoute(array &$routes, \ReflectionClass $class, \ReflectionMethod $method, $annotation, $globals)
    {
        $name = $annotation->getName() ?: $this->getDefaultRouteName($method);
        $path = $annotation->getPath() ?: $this->getDefaultRoutePath($method);

        $routes[] = (new Route(rtrim($globals['path'].$path, '/')))
            ->setName($globals['name'].'/'.$name)
            ->setDefaults(array_replace($globals['defaults'], $annotation->getDefaults(), ['_controller' => $class->name.'::'.$method->name]))
            ->setRequirements(array_replace($globals['requirements'], $annotation->getRequirements()))
            ->setOptions(array_replace($globals['options'], $annotation->getOptions()))
            ->setHost($annotation->getHost() ?: $globals['host'])
            ->setSchemes(array_replace($globals['schemes'], $annotation->getSchemes()))
            ->setMethods(array_replace($globals['methods'], $annotation->getMethods()))
            ->setCondition($annotation->getCondition() ?: $globals['condition']);
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
     * @param  \ReflectionMethod $method
     * @return string
     */
    protected function getDefaultRoutePath(\ReflectionMethod $method)
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
     * @param  \ReflectionMethod $method
     * @return string
     */
    protected function getDefaultRouteName(\ReflectionMethod $method)
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
