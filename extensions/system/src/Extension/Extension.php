<?php

namespace Pagekit\Extension;

use Pagekit\Component\File\ResourceLocator;
use Pagekit\Component\Routing\Controller\ControllerCollection;
use Pagekit\Framework\Application;
use Pagekit\Framework\ApplicationTrait;
use Symfony\Component\Translation\Translator;

class Extension implements \ArrayAccess
{
    use ApplicationTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \ReflectionObject
     */
    protected $reflected;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $path
     * @param array  $config
     */
    public function __construct($name, $path, array $config = [])
    {
        $this->name   = $name;
        $this->path   = $path;
        $this->config = $config;
    }

    /**
     * Boots the extension.
     */
    public function boot(Application $app)
    {
        $this->registerControllers($app['controllers']);
        $this->registerLanguages($app['translator']);
        $this->registerResources($app['locator']);
    }

    /**
     * Returns the extensions's name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the extensions's absolute path.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns the extension's config
     *
     * @param mixed $key
     * @param mixed $default
     * @return array
     */
    public function getConfig($key = null, $default = null)
    {
        if (null === $key) {
            return $this->config;
        }

        $array = $this->config;

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
        if (isset($this->config['controllers'])) {
            $controllers = (array) $this->config['controllers'];
            foreach ($controllers as $controller) {
                foreach (glob($this->getPath().'/'.ltrim($controller, '/')) as $file) {

                    $path = strtolower(sprintf('%s/%s', $this->getName(), basename($file, 'Controller.php')));
                    $name = '@'.$path;

                    $collection->add($file, compact('path', 'name'));
                }
            }
        }
    }

    /**
     * Finds and registers languages.
     *
     * Override this method if your extension does not follow the conventions:
     *
     *  - Languages are in the 'languages' sub-directory
     *  - The naming convention '/locale/domain.format', example: /en_GB/hello.mo
     *
     * @param Translator $translator
     */
    public function registerLanguages(Translator $translator)
    {
        $files = glob($this->getPath().'/languages/*/*.{mo,po,php}', GLOB_BRACE) ?: [];

        foreach ($files as $file) {
            if (preg_match('/languages\/(.+)\/(.+)\.(.+)$/', $file, $matches)) {

                list(, $locale, $domain, $format) = $matches;

                if ($format == 'php') {
                    $format = 'array';
                    $file = require($file);
                }

                $translator->addResource($format, $file, $locale, $domain);
                $translator->addResource($format, $file, substr($locale, 0, 2), $domain);
            }
        }
    }

    /**
     * Finds and adds extension file resources.
     *
     * @param ResourceLocator $locator
     */
    public function registerResources(ResourceLocator $locator)
    {
        $root = $this->getPath();

        $addResources = function($config, $prefix = '') use ($root, $locator) {
            foreach ($config as $scheme => $resources) {

                if (strpos($scheme, '://') > 0 && $segments = explode('://', $scheme, 2)) {
                    list($scheme, $prefix)  = $segments;
                }

                $resources = (array) $resources;

                array_walk($resources, function(&$resource) use ($root) {
                    $resource = "$root/$resource";
                });

                $locator->addPath($scheme, $prefix, $resources);
            }
        };

        $addResources($this->getConfig('resources.export', []), $this->getName());

        if ($config = $this->getConfig('resources.override')) {
            $this['events']->addListener('system.init', function() use ($config, $addResources) {
                $addResources($config);
            }, 20);
        }
    }

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
