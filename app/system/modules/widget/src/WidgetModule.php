<?php

namespace Pagekit\Widget;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\Widget\Model\TypeInterface;

class WidgetModule extends Module
{
    protected $types = [];

    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app['widget'] = $this;

        $app['module']->addFactory('widget', function ($module) use ($app) {

            $class = is_string($module['main']) ? $module['main'] : 'Pagekit\Widget\Model\Type';

            $module = new $class($module);
            $module->main($app);

            $this->registerType($module->name, $module);

            return $module;
        });

        $app['position'] = function ($app) {

            $positions = new PositionManager($app->config($app['theme']->name));

            foreach ($app['theme']->get('positions', []) as $name => $label) {
                $positions->register($name, $label);
            }

            return $positions;

        };

        $app->extend('view', function ($view) use ($app) {

            if ($app['theme']) {
                $view->addHelper(new PositionHelper($app['position']));
            }

            return $view;
        });
    }

    /**
     * @param  string $type
     * @return TypeInterface
     */
    public function getType($type)
    {
        return isset($this->types[$type]) ? $this->types[$type] : null;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @param string        $name
     * @param TypeInterface $type
     */
    public function registerType($name, TypeInterface $type)
    {
        $this->types[$name] = $type;
    }
}
