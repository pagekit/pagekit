<?php

namespace Pagekit\Routing\Controller;

use Pagekit\Routing\Event\RouteCollectionEvent;
use Pagekit\Routing\Event\RouteResourcesEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class AliasCollection implements EventSubscriberInterface
{
    /**
     * @var array
     */
    protected $aliases = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->routes = new RouteCollection;
    }

    /**
     * Adds an alias.
     *
     * @param string $path
     * @param string $name
     * @param array  $defaults
     */
    public function add($path, $name, array $defaults = [])
    {
        $path = preg_replace('/^[^\/]/', '/$0', $path);

        $this->aliases[$name] = [$path, $defaults];
    }

    /**
     * Gets an alias.
     *
     * @param  string $name
     * @return array
     */
    public function get($name)
    {
        return isset($this->aliases[$name]) || isset($this->aliases[$name = strtok($name, '?')]) ? $this->aliases[$name] : false;
    }

    /**
     * Adds this instances routes to the collection.
     *
     * @param RouteCollectionEvent $event
     */
    public function getRoutes(RouteCollectionEvent $event)
    {
        $routes = $event->getRoutes();
        foreach ($this->aliases as $source => $alias) {

            $name   = $source;
            $params = $alias[1];

            if ($query = substr(strstr($name, '?'), 1)) {
                parse_str($query, $params);
                $name = strstr($name, '?', true);
            }

            if ($route = $routes->get($name)) {
                $routes->add($source, new Route($alias[0], array_merge($route->getDefaults(), $params, ['_variables' => $route->compile()->getPathVariables()])));
            }
        }
    }

    /**
     * Adds this instances resources to the collection
     *
     * @param RouteResourcesEvent $event
     */
    public function getResources(RouteResourcesEvent $event)
    {
        $resources = [];
        foreach ($this->aliases as $name => $alias) {
            $resources[] = ['alias' => $name.$alias[0]];
        }

        $event->addResources($resources);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'route.collection' => ['getRoutes', -16],
            'route.resources' => 'getResources'
        ];
    }
}
