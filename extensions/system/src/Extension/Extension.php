<?php

namespace Pagekit\Extension;

use Pagekit\Application as App;
use Pagekit\Module\ModuleInterface;
use Pagekit\Routing\Controller\ControllerCollection;

class Extension implements ModuleInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var \ReflectionObject
     */
    protected $reflected;

    /**
     * Loads the extension.
     */
    public function load(App $app, array $config)
    {
        $this->config = $config;

        $this->registerControllers($app['controllers']);

        $this->registerLanguages();
        // -TODO- $this->registerResources($app['locator']);

        $app->on('system.init', [$this, 'registerLanguages']);

        if ($this->getConfig('parameters.settings')) {

            if (is_array($defaults = $this->getConfig('parameters.settings.defaults'))) {
                $this->parameters = array_replace($this->parameters, $defaults);
            }

            if (is_array($settings = App::option("{$config['name']}:settings"))) {
                $this->parameters = array_replace($this->parameters, $settings);
            }
        }
    }

    /**
     * Returns the extensions's name.
     */
    public function getName()
    {
        return $this->config['name'];
    }

    /**
     * Returns the extensions's absolute path.
     */
    public function getPath()
    {
        return $this->config['path'];
    }

    /**
     * Returns the extension's config.
     *
     * @param  mixed $key
     * @param  mixed $default
     * @return array
     */
    public function getConfig($key = null, $default = null)
    {
        return $this->fetch($this->config, $key, $default);
    }

    /**
     * Returns the extension's parameters.
     *
     * @param  mixed $key
     * @param  mixed $default
     * @return array
     */
    public function getParams($key = null, $default = null)
    {
        return $this->fetch($this->parameters, $key, $default);
    }

    /**
     * Returns a value from given array.
     *
     * @param  array $array
     * @param  mixed $key
     * @param  mixed $default
     * @return array
     */
    protected function fetch($array, $key, $default)
    {
        if (null === $key) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {

            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Finds and registers controllers.
     *
     * Override this method if your extension controllers do not follow the conventions:
     *
     *  - The controller folder is defined in the extensions config
     *  - The naming convention is 'HelloController.php'
     *
     * @param ControllerCollection $collection
     */
    public function registerControllers(ControllerCollection $collection)
    {
        if (!isset($this->config['controllers'])) {
            return;
        }

        foreach ((array) $this->config['controllers'] as $prefix => $controllers) {

            if (false === strpos($prefix, ':')) {
                $namespace = "@{$this->getName()}";
            } else {
                list($namespace, $prefix) = explode(':', $prefix);
            }

            $collection->mount($prefix, $controllers, "$namespace/");
        }
    }

    /**
     * Finds and registers languages.
     *
     * Override this method if your extension does not follow the conventions:
     *
     *  - Languages are in the 'languages' sub-directory
     *  - The naming convention '/locale/domain.format', example: /en_GB/hello.php
     */
    public function registerLanguages()
    {
        $locale = App::translator()->getLocale();
        $domains = [];
        foreach (glob($this->getPath().'/languages/'.$locale.'/*') ?: [] as $file) {

            $format = substr(strrchr($file, '.'), 1);
            $domain = basename($file, '.'.$format);

            if (in_array($domain, $domains)) {
                continue;
            }

            $domains[] = $domain;

            App::translator()->addResource($format, $file, $locale, $domain);
            App::translator()->addResource($format, $file, substr($locale, 0, 2), $domain);
        }
    }

    /**
     * Finds and adds extension file resources.
     *
     * @param ResourceLocator $locator
     */
    // public function registerResources(ResourceLocator $locator)
    // {
    //     $root = $this->getPath();

    //     $addResources = function($config, $prefix = '') use ($root, $locator) {
    //         foreach ($config as $scheme => $resources) {

    //             if (strpos($scheme, '://') > 0 && $segments = explode('://', $scheme, 2)) {
    //                 list($scheme, $prefix)  = $segments;
    //             }

    //             $resources = (array) $resources;

    //             array_walk($resources, function(&$resource) use ($root) {
    //                 $resource = "$root/$resource";
    //             });

    //             $locator->addPath($scheme, $prefix, $resources);
    //         }
    //     };

    //     $addResources($this->getConfig('resources.export', []), $this->getName());

    //     if ($config = $this->getConfig('resources.override')) {
    //         App::on('system.init', function() use ($config, $addResources) {
    //             $addResources($config);
    //         }, 20);
    //     }
    // }

    /**
     * Extension's enable hook
     */
    public function enable()
    {
    }

    /**
     * Extension's disable hook
     */
    public function disable()
    {
    }

    /**
     * Extension's uninstall hook
     */
    public function uninstall()
    {
    }
}
