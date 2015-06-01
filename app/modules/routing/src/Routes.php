<?php

namespace Pagekit\Routing;

class Routes implements \IteratorAggregate
{
    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @var \Closure[]
     */
    protected $callbacks = [];

    /**
     * Adds a route.
     *
     * @param  string $name
     * @param  mixed  $route
     * @return Route
     */
    public function add($name, $route)
    {
        if (is_array($route)) {
            $route = $this->createRoute($name, $route);
        }

        return $this->routes[] = $route;
    }

    /**
     * Maps a route to a callback (GET).
     *
     * @param  string   $path
     * @param  string   $name
     * @param  callable $callback
     * @return Route
     */
    public function get($path, $name, $callback)
    {
        return $this->match($path, $name, $callback)->setMethods('GET');
    }

    /**
     * Maps a route to a callback (POST).
     *
     * @param  string   $path
     * @param  string   $name
     * @param  callable $callback
     * @return Route
     */
    public function post($path, $name, $callback)
    {
        return $this->match($path, $name, $callback)->setMethods('POST');
    }

    /**
     * Maps a route to a callback.
     *
     * @param  string   $path
     * @param  string   $name
     * @param  callable $callback
     * @return Route
     */
    public function match($path, $name, $callback)
    {
        return $this->add($name, ['path' => $path, 'controller' => $callback]);
    }

    /**
     * Gets a registered callback.
     *
     * @param  string $name
     * @return callable|null
     */
    public function getCallback($name)
    {
        return isset($this->callbacks[$name]) ? $this->callbacks[$name] : null;
    }

    /**
     * Implements the \IteratorAggregate.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
    }

    /**
     * Creates a route from array definition.
     *
     * @param  string $name
     * @param  array  $config
     * @return Route
     */
    protected function createRoute($name, array $config)
    {
        $defaults = isset($config['defaults']) ? $config['defaults'] : [];
        $requirements = isset($config['requirements']) ? $config['requirements'] : [];
        $options = isset($config['options']) ? $config['options'] : [];
        $host = isset($config['host']) ? $config['host'] : '';
        $schemes = isset($config['schemes']) ? $config['schemes'] : [];
        $methods = isset($config['methods']) ? $config['methods'] : [];
        $condition = isset($config['condition']) ? $config['condition'] : '';
        
        $options['controller'] = isset($config['controller']) ? $config['controller'] : '';

        if (is_callable($options['controller'])) {
            $this->callbacks[$name] = $options['controller'];
        } else {
            unset ($this->callbacks[$name]);
        }

        return (new Route($config['path'], $defaults, $requirements, $options, $host, $schemes, $methods, $condition))->setName($name);
    }
}
