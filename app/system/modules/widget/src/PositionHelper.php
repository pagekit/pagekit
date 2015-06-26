<?php

namespace Pagekit\Widget;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\View\Helper\Helper;

class PositionHelper extends Helper
{
    /**
     * @var Module
     */
    protected $widgets;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->widgets = App::module('system/widget');
    }

    /**
     * Set shortcut.
     *
     * @see render()
     */
    public function __invoke($name, $view = null, array $parameters = [])
    {
        return $this->render($name, $view, $parameters);
    }

    /**
     * Checks if the position exists.
     *
     * @param  string $name
     * @return bool
     */
    public function exists($name)
    {
        return (bool) $this->widgets->getPositions()->getWidgets($name);
    }

    /**
     * Renders a position.
     *
     * @param  string       $name
     * @param  array|string $view
     * @param  array        $parameters
     * @return string
     */
    public function render($name, $view = null, array $parameters = [])
    {
        if (is_array($view)) {
            $parameters = $view;
            $view = false;
        }

        $parameters['widgets'] = $this->widgets->getPositions()->getWidgets($name);

        if (!$view) {

            $result = '';

            foreach ($parameters['widgets'] as $widget) {
                $result .= $widget->get('result');
            }

            return $result;
        }

        return $this->view->render($view, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'position';
    }
}
