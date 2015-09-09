<?php

namespace Pagekit\Module;

use Pagekit\Application;
use Pagekit\Module\Factory\CallableFactory;
use Pagekit\Module\Factory\FactoryInterface;
use Pagekit\Module\Factory\ModuleFactory;
use Pagekit\Module\Loader\CallableLoader;
use Pagekit\Module\Loader\LoaderInterface;

class ModuleManager implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $paths = [];

    /**
     * @var array
     */
    protected $modules = [];

    /**
     * @var array
     */
    protected $registered = [];

    /**
     * @var LoaderInterface[]
     */
    protected $loaders = [];

    /**
     * @var FactoryInterface[]
     */
    protected $factories = [];

    /**
     * Constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->addFactory('module', new ModuleFactory($app));
    }

    /**
     * Get shortcut.
     *
     * @see get()
     */
    public function __invoke($name)
    {
        return $this->get($name);
    }

    /**
     * Gets a module.
     *
     * @param  string $name
     * @return mixed|null
     */
    public function get($name)
    {
        return isset($this->modules[$name]) ? $this->modules[$name] : null;
    }

    /**
     * Gets all modules.
     *
     * @return array
     */
    public function all()
    {
        return $this->modules;
    }

    /**
     * Loads modules by name.
     *
     * @param  string|array $modules
     * @return self
     */
    public function load($modules)
    {
        $resolved = [];

        if (is_string($modules)) {
            $modules = (array)$modules;
        }

        $this->registerModules();

        foreach ((array)$modules as $name) {

            if (!isset($this->registered[$name])) {
                throw new \RuntimeException("Undefined module: $name");
            }

            $this->resolveModules($this->registered[$name], $resolved);
        }

        $resolved = array_diff_key($resolved, $this->modules);

        foreach ($resolved as $name => $module) {

            foreach ($this->loaders as $loader) {
                $module = $loader->load($name, $module);
            }

            if ($factory = $this->getFactory($module['type'])) {
                $this->modules[$name] = $factory->create($module);

                if (is_a($this->modules[$name], 'Pagekit\Event\EventSubscriberInterface')) {
                    $this->app->subscribe($this->modules[$name]);
                }
            }
        }

        return $this;
    }

    /**
     * Adds a module path(s).
     *
     * @param  string|array $paths
     * @return self
     */
    public function addPath($paths)
    {
        $this->paths = array_merge($this->paths, (array)$paths);

        return $this;
    }

    /**
     * Adds a module loader.
     *
     * @param  LoaderInterface|callable $loader
     * @return self
     */
    public function addLoader($loader)
    {
        if (is_callable($loader)) {
            $loader = new CallableLoader($loader);
        }

        $this->loaders[] = $loader;

        return $this;
    }

    /**
     *  Gets a module factory.
     *
     * @param  string $type
     * @return FactoryInterface
     */
    public function getFactory($type)
    {
        return isset($this->factories[$type]) ? $this->factories[$type] : null;
    }

    /**
     * Adds a module factory.
     *
     * @param  string $type
     * @param  FactoryInterface|callable $factory
     * @return self
     */
    public function addFactory($type, $factory)
    {
        if (is_callable($factory)) {
            $factory = new CallableFactory($factory);
        }

        $this->factories[$type] = $factory;

        return $this;
    }

    /**
     * Checks if a module exists.
     *
     * @param  string $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return isset($this->modules[$name]);
    }

    /**
     * Gets a module by name.
     *
     * @param  string $name
     * @return bool
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * Sets a module.
     *
     * @param string $name
     * @param string $module
     */
    public function offsetSet($name, $module)
    {
        $this->modules[$name] = $module;
    }

    /**
     * Unset a module.
     *
     * @param string $name
     */
    public function offsetUnset($name)
    {
        unset($this->modules[$name]);
    }

    /**
     * Implements the IteratorAggregate.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->modules);
    }

    /**
     * Register modules from paths.
     */
    protected function registerModules()
    {
        $includes = [];
        $defaults = ['type' => 'module', 'main' => null, 'config' => []];

        $app = $this->app;

        foreach ($this->paths as $path) {

            $paths = glob($path, GLOB_NOSORT) ?: [];

            foreach ($paths as $p) {

                if (!is_array($module = include $p) || !isset($module['name'])) {
                    continue;
                }

                $module = array_replace($defaults, $module);
                $module['path'] = strtr(dirname($p), '\\', '/');

                if (isset($module['include'])) {
                    foreach ((array)$module['include'] as $include) {
                        $includes[] = $this->resolvePath($module, $include);
                    }
                }

                $this->registered[$module['name']] = $module;
            }
        }

        if ($this->paths = $includes) {
            $this->registerModules();
        }
    }

    /**
     * Resolves module requirements.
     *
     * @param array $module
     * @param array $resolved
     * @param array $unresolved
     *
     * @throws \RuntimeException
     */
    protected function resolveModules($module, &$resolved = [], &$unresolved = [])
    {
        $unresolved[$module['name']] = $module;

        if (isset($module['require'])) {
            foreach ((array)$module['require'] as $required) {
                if (!isset($resolved[$required])) {

                    if (isset($unresolved[$required])) {
                        throw new \RuntimeException(sprintf('Circular requirement "%s > %s" detected.', $module['name'], $required));
                    }

                    if (isset($this->registered[$required])) {
                        $this->resolveModules($this->registered[$required], $resolved, $unresolved);
                    }
                }
            }
        }

        $resolved[$module['name']] = $module;
        unset($unresolved[$module['name']]);
    }

    /**
     * Resolves a path to a absolute module path.
     *
     * @param  array $module
     * @param  string $path
     * @return string
     */
    protected function resolvePath($module, $path)
    {
        $path = strtr($path, '\\', '/');

        if (!($path[0] == '/' || (strlen($path) > 3 && ctype_alpha($path[0]) && $path[1] == ':' && $path[2] == '/'))) {
            $path = $module['path'] . "/$path";
        }

        return $path;
    }
}
