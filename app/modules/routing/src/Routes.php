<?php

namespace Pagekit\Routing;

class Routes implements \IteratorAggregate, \Serializable
{
    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @var callable[]
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
     * @param  callable $callback
     * @return Route
     */
    public function get($path, $callback)
    {
        return $this->match($path, $callback)->setMethods('GET');
    }

    /**
     * Maps a route to a callback (POST).
     *
     * @param  string   $path
     * @param  callable $callback
     * @return Route
     */
    public function post($path, $callback)
    {
        return $this->match($path, $callback)->setMethods('POST');
    }

    /**
     * Maps a route to a callback.
     *
     * @param  string   $path
     * @param  callable $callback
     * @return Route
     */
    public function match($path, $callback)
    {
        return $this->add($path, ['path' => $path, 'controller' => $callback]);
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
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize($this->routes);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $this->routes = unserialize($serialized);
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
            $options['controller'] = '__callback';
        } else {
            unset ($this->callbacks[$name]);
        }

        return (new Route($config['path'], $defaults, $requirements, $options, $host, $schemes, $methods, $condition))->setName($name);
    }
}
