<?php

namespace Pagekit\System;

use Pagekit\Application as App;
use Pagekit\View\Section\SectionManager;
use Pagekit\View\ViewInterface;

class Theme extends Module
{
    /**
     * @var string
     */
    protected $layout = '/templates/template.razr';

    /**
     * {@inheritdoc}
     */
    public function __construct(App $app, array $config)
    {
        parent::__construct($app, $config);

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
