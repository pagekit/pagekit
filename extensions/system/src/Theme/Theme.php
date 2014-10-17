<?php

namespace Pagekit\Theme;

use Pagekit\Component\File\ResourceLocator;
use Pagekit\Component\View\Section\SectionManager;
use Pagekit\Component\View\ViewInterface;
use Pagekit\Framework\Application;
use Pagekit\Framework\ApplicationTrait;
use Symfony\Component\Translation\Translator;

class Theme implements \ArrayAccess
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
     * @var array
     */
    protected $parameters = [];

    /**
     * @var string
     */
    protected $layout = '/templates/template.razr';

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
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        $this->registerLanguages($app['translator']);
        $this->registerResources($app['locator']);

        if ($this->getConfig('parameters.settings')) {

            if (is_array($defaults = $this->getConfig('parameters.settings.defaults'))) {
                $this->parameters = array_replace($this->parameters, $defaults);
            }

            if (is_array($settings = $this['option']->get("{$this->name}:settings"))) {
                $this->parameters = array_replace($this->parameters, $settings);
            }
        }

        $app->on('system.site', function() use ($app) {
            $this->registerRenderer($app['view.sections'], $app['view']);
        });

        $app->on('system.positions', function($event) {
            foreach ($this->getConfig('positions', []) as $id => $position) {
                list($name, $description) = array_merge((array) $position, ['']);
                $event->register($id, $name, $description);
            }
        });
    }

    /**
     * Returns the theme name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the theme absolute path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns the theme layout absolute path.
     *
     * @return string|false
     */
    public function getLayout()
    {
        return $this->path.$this->layout;
    }

    /**
     * Returns the theme's config.
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
     * Returns the theme's parameters.
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
     * Finds and registers languages.
     *
     * Override this method if your theme does not follow the conventions:
     *
     *  - Languages are in the 'languages' sub-directory
     *  - The naming convention '/locale/domain.format', example: /en_GB/mytheme.mo
     *
     * @param Translator $translator
     */
    public function registerLanguages(Translator $translator)
    {
        $files = glob($this->getPath().'/languages/*/*') ?: [];

        foreach ($files as $file) {
            if (preg_match('/languages\/(.+)\/(.+)\.(mo|po|php)$/', $file, $matches)) {

                list(, $locale, $domain, $format) = $matches;

                if ($format == 'php') {
                    $format = 'array';
                    $file = require $file;
                }

                $translator->addResource($format, $file, $locale, $domain);
                $translator->addResource($format, $file, substr($locale, 0, 2), $domain);
            }
        }
    }

    /**
     * Finds and adds theme file resources.
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
        $addResources($this->getConfig('resources.override', []), $this->getName());
    }

    /**
     * Adds section renderer.
     *
     * @param SectionManager $sections
     * @param ViewInterface  $view
     */
    public function registerRenderer(SectionManager $sections, ViewInterface $view)
    {
        foreach ($this->getConfig('renderer', []) as $name => $template) {
            $sections->addRenderer($name, function($name, $value, $options = []) use ($template, $view) {
                return $view->render($template, compact('name', 'value', 'options'));
            });
        }
    }
}
