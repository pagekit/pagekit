<?php

namespace Pagekit\Module;

use Pagekit\Application;
use Pagekit\Module\Loader\CallableLoader;
use Pagekit\Module\Loader\LoaderInterface;
use Pagekit\Module\Loader\ModuleLoader;

class ModuleManager implements \IteratorAggregate
{
    /**
     * @var Application
     */
    protected $app;

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
    protected $preLoaders = [];

    /**
     * @var LoaderInterface[]
     */
    protected $postLoaders = [];

    /**
     * @var array
     */
    protected $defaults = [
        'main' => null,
        'type' => 'module',
        'class' => 'Pagekit\Module\Module',
        'config' => []
    ];

    /**
     * Constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->postLoaders = [new ModuleLoader($app)];
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
            $modules = (array) $modules;
        }

        foreach ((array) $modules as $name) {

            if (!isset($this->registered[$name])) {
                throw new \RuntimeException("Undefined module: $name");
            }

            $this->resolveModules($this->registered[$name], $resolved);
        }

        $resolved = array_diff_key($resolved, $this->modules);

        foreach ($resolved as $name => $module) {

            foreach ($this->preLoaders as $loader) {
                $module = $loader->load($module);
            }

            foreach ($this->postLoaders as $loader) {
                $module = $loader->load($module);
            }

            $this->modules[$name] = $module;
        }

        return $this;
    }

    /**
     * Registers modules from path(s).
     *
     * @param  string|array $paths
     * @param  string $basePath
     * @return self
     */
    public function register($paths, $basePath = null)
    {
        $app = $this->app;
        $includes = [];

        foreach ((array) $paths as $path) {

            $files = glob($this->resolvePath($path, $basePath), GLOB_NOSORT) ?: [];

            foreach ($files as $file) {

                if (!is_array($module = include $file) || !isset($module['name'])) {
                    continue;
                }

                $module = array_replace($this->defaults, $module);
                $module['path'] = strtr(dirname($file), '\\', '/');

                if (isset($module['include'])) {
                    foreach ((array) $module['include'] as $include) {
                        $includes[] = $this->resolvePath($include, $module['path']);
                    }
                }

                $this->registered[$module['name']] = $module;
            }
        }

        if ($includes) {
            $this->register($includes);
        }

        return $this;
    }

    /**
     * Adds a module loader.
     *
     * @param  LoaderInterface|callable $loader
     * @param  boolean $post
     * @return self
     */
    public function addLoader($loader, $post = false)
    {
        if (is_callable($loader)) {
            $loader = new CallableLoader($loader);
        }

        if (!$post) {
            $this->preLoaders[] = $loader;
        } else {
            $this->postLoaders[] = $loader;
        }

        return $this;
    }

    /**
     * Implements the IteratorAggregate.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->all());
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
    protected function resolveModules(array $module, array &$resolved = [], array &$unresolved = [])
    {
        $unresolved[$module['name']] = $module;

        if (isset($module['require'])) {
            foreach ((array) $module['require'] as $required) {
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
     * Resolves a absolute path to a given base path.
     *
     * @param  string $path
     * @param  string $basePath
     * @return string
     */
    protected function resolvePath($path, $basePath = null)
    {
        $path = strtr($path, '\\', '/');

        if (!($path[0] == '/' || (strlen($path) > 3 && ctype_alpha($path[0]) && $path[1] == ':' && $path[2] == '/'))) {
            $path = "$basePath/$path";
        }

        return $path;
    }
}
