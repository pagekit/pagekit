<?php

namespace Pagekit\System;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\View\Section\SectionManager;
use Pagekit\View\ViewInterface;

class Theme extends Module
{
    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var string
     */
    protected $layout = '/templates/template.razr';

    /**
     * {@inheritdoc}
     */
    public function load(App $app, array $config)
    {
        if ($this->getConfig('parameters.settings')) {

            if (is_array($defaults = $this->getConfig('parameters.settings.defaults'))) {
                $this->parameters = array_replace($this->parameters, $defaults);
            }

            if (is_array($settings = App::option("{$config['name']}:settings"))) {
                $this->parameters = array_replace($this->parameters, $settings);
            }
        }

        $app->on('system.site', function() use ($app) {
            $this->registerRenderer($app['sections'], $app['view']);
        });

        $app->on('system.positions', function($event) {
            foreach ($this->getConfig('positions', []) as $id => $position) {
                list($name, $description) = array_merge((array) $position, ['']);
                $event->register($id, $name, $description);
            }
        });

        $app->on('site.menus', function($event) use ($app) {
            foreach ($this->getConfig('menus', []) as $id => $menu) {
                $event->register($id, $menu, ['fixed' => true]);
            }
        });
    }

    /**
     * Returns the theme layout absolute path.
     *
     * @return string|false
     */
    public function getLayout()
    {
        return $this->getPath().$this->layout;
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
        if (null === $key) {
            return $this->parameters;
        }

        $array = $this->parameters;

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
