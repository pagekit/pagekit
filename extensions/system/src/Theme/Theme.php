<?php

namespace Pagekit\Theme;

use Pagekit\Component\File\ResourceLocator;
use Pagekit\Framework\Application;
use Pagekit\Framework\ApplicationAware;
use Symfony\Component\Translation\Translator;

class Theme extends ApplicationAware
{
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
     * @var string
     */
    protected $layout = '/templates/template.razr.php';

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $path
     * @param array  $config
     */
    public function __construct($name, $path, array $config = array())
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
        $this->config += $app['option']->get("{$this->name}:config", array());

        $this->registerLanguages($app['translator']);
        $this->registerResources($app['locator']);

        if ($renderer = $this->getConfig('renderer')) {
            $app->on('system.position.renderer', function($event) use ($renderer) {
                foreach ($renderer as $name => $template) {
                    $event->register($name, $template);
                }
            });
        }
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
     * Returns the theme's config
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
        foreach (glob($this->getPath().'/languages/*/*') as $file) {
            if (preg_match('/languages\/(.+)\/(.+)\.(mo|po|php)$/', $file, $matches)) {

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

        $addResources($this->getConfig('resources.export', array()), $this->getName());
        $addResources($this->getConfig('resources.override', array()), $this->getName());
    }
}
