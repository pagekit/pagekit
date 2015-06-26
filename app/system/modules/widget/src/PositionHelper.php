<?php

namespace Pagekit\Widget;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\View\Helper\Helper;
use Pagekit\Widget\Entity\Widget;
use Pagekit\Widget\Model\WidgetInterface;

class PositionHelper extends Helper
{
    /**
     * @var Module
     */
    protected $module;

    /**
     * @var WidgetInterface[]
     */
    protected $widgets;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->module = App::module('system/widget');
    }

    /**
     * Set shortcut.
     *
     * @see get()
     */
    public function __invoke($name)
    {
        return $this->get($name);
    }

    /**
     * Gets widgets for a position.
     *
     * @param  string $name
     * @return WidgetInterface[]
     */
    public function get($name)
    {
        $widgets  = $this->getWidgets();
        $assigned = $this->module->getPositions()->getAssigned($name);

        return array_filter(array_map(function($id) use ($widgets) {
            return (isset($widgets[$id])
                and $widget = $widgets[$id]
                and $widget->hasAccess(App::user())
                and (!$nodes = $widget->getNodes() or in_array(App::node()->getId(), $nodes))
                and $type = $this->module->getType($widget->getType())
            ) ? $widget : null;
        }, $assigned));
    }

    /**
     * Checks if the position exists.
     *
     * @param  string $name
     * @return bool
     */
    public function exists($name)
    {
        return (bool) $this->get($name);
    }

    /**
     * Renders a position.
     *
     * @param string       $name
     * @param array|string $view
     * @param array        $options
     */
    public function render($name, $view = null, array $options = [])
    {
        if (is_array($view)) {
            $options = $view;
        }

        $widgets = $this->get($name);

        foreach ($widgets as $widget) {
            $type = $this->module->getType($widget->getType());
            $widget->set('result', $type->render($widget));
        }

        $options['widgets'] = $widgets;

        if ($view) {
            echo $this->view->render($view, $options);
        } else {

            foreach ($widgets as $widget) {
                echo $widget->get('result');
            }

        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'position';
    }

    /**
     * @return WidgetInterface[]
     */
    protected function getWidgets()
    {
        if (null === $this->widgets) {
            $this->widgets = Widget::findAll();
        }

        return $this->widgets;
    }
}
