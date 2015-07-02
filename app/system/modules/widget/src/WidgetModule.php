<?php

namespace Pagekit\Widget;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\Widget\Entity\Widget;
use Pagekit\Widget\Model\TypeInterface;
use Pagekit\Widget\Model\WidgetInterface;

class WidgetModule extends Module
{
    protected $types = [];
    protected $widgets;

    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        // $this->config->merge(['widget' => ['defaults' => $app['theme']->config('widget.defaults', [])]]);

        $app['module']->addFactory('widget', function ($module) use ($app) {

            $class = is_string($module['main']) ? $module['main'] : 'Pagekit\Widget\Model\Type';

            $module = new $class($module);
            $module->main($app);

            $this->registerType($module->name, $module);

            return $module;
        });

        $app['positions'] = function($app) {

            $positions = new PositionManager($this->config('widget.positions'));

            foreach ((array) $app['theme']->get('positions') as $name => $position) {
                list($label, $description) = array_merge((array) $position, ['']);
                $positions->register($name, $label, $description);
            }

            return $positions;
        };
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
     * Registers a type.
     *
     * @param string        $name
     * @param TypeInterface $type
     */
    public function registerType($name, TypeInterface $type)
    {
        $this->types[$name] = $type;
    }

    /**
     * Gets active widgets.
     *
     * @param  string $position
     * @return WidgetInterface[]
     */
    public function getWidgets($position = null)
    {
        if ($this->widgets === null) {

            foreach (App::positions()->getAssigned() as $name => $ids) {

                $widgets = Widget::findAll();
                $node    = App::node()->getId();

                foreach ($ids as $id) {

                    if (!isset($widgets[$id])
                        or !$widget = $widgets[$id]
                        or !$widget->hasAccess(App::user())
                        or ($nodes = $widget->getNodes() and !in_array($node, $nodes))
                        or !$type = $this->getType($widget->getType())
                    ) {
                        continue;
                    }

                    $widget->set('result', $type->render($widget));

                    $this->widgets[$name][] = $widget;

                }

            }

        }

        if ($position === null) {
            return $this->widgets;
        }

        return isset($this->widgets[$position]) ? $this->widgets[$position] : [];
    }
}
