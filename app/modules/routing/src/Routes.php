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
     * @var array[]
     */
    protected $aliases = [];

    /**
     * Adds a route.
     *
     * @param  mixed $route
     * @return Route
     */
    public function add($route)
    {
        if (is_array($route)) {
            $route = $this->createRoute($route);
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
        return $this->add(['path' => $path, 'controller' => $callback]);
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
     * Adds an alias.
     *
     * @param string $path
     * @param string $name
     * @param array  $defaults
     */
    public function alias($path, $name, array $defaults = [])
    {
        $path = preg_replace('/^[^\/]/', '/$0', $path);

        $this->aliases[$name] = [$name, $path, $defaults];
    }

    /**
     * Gets aliases.
     *
     * @return array[]
     */
    public function getAliases()
    {
        return $this->aliases;
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
        return serialize([$this->routes, $this->aliases]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->routes, $this->aliases) = unserialize($serialized);
    }

    /**
     * Creates a route from array definition.
     *
     * @param  array $config
     * @return Route
     */
    protected function createRoute(array $config)
    {
        $name = isset($config['name']) ? $config['name'] : $config['path'];
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
