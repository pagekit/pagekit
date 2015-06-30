<?php

namespace Pagekit\View\Helper;

use Pagekit\Application as App;

class PositionHelper extends Helper
{
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
        return (bool) App::module('system/widget')->getWidgets($name);
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

        $parameters['widgets'] = App::module('system/widget')->getWidgets($name);

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
