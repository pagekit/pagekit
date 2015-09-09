<?php

namespace Pagekit\Widget;

use Pagekit\Application as App;
use Pagekit\View\Helper\Helper;
use Pagekit\Widget\Model\Widget;

class PositionHelper extends Helper
{
    /**
     * @var PositionManager
     */
    protected $positions;

    /**
     * @param PositionManager $positions
     */
    public function __construct(PositionManager $positions)
    {
        $this->positions = $positions;
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
        return (bool) $this->getWidgets($name);
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

        $parameters['widgets'] = $this->getWidgets($name);

        return $this->view->render($view ?: 'system/site/position.php', $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'position';
    }

    /**
     * @param  string|null $position
     * @return Widget[]
     */
    protected function getWidgets($position)
    {
        static $widgets, $positions = [];

        if (null === $widgets) {
            $widgets = Widget::where(['status' => 1])->get();
        }

        if (!$pos = $this->positions->get($position)) {
            return [];
        }

        if (!isset($positions[$position])) {

            $positions[$position] = [];

            foreach ($pos['assigned'] as $id) {

                if (!isset($widgets[$id])
                    or !$widget = $widgets[$id]
                    or !$widget->hasAccess(App::user())
                    or ($nodes = $widget->nodes and !in_array(App::node()->id, $nodes))
                    or !$type = App::widget($widget->type)
                ) {
                    continue;
                }

                $result = $type->render($widget);

                $widget->set('result', $result);
                $positions[$position][] = $widget;
            }
        }

        return $positions[$position];
    }
}
