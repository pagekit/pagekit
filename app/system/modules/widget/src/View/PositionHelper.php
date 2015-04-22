<?php

namespace Pagekit\Widget\View;

use Pagekit\View\Helper\HelperInterface;
use Pagekit\View\ViewManager;
use Pagekit\Widget\Model\WidgetInterface;

class PositionHelper implements HelperInterface
{
    /**
     * @var ViewManager
     */
    protected $view;

    /**
     * @var array
     */
    protected $positions = [];

    /**
     * Constructor.
     *
     * @param ViewManager $view
     */
    public function __construct(ViewManager $view)
    {
        $this->view = $view;

        $view->map('position.default', 'widget:views/widgets.php');

        $this->view->on('render', function ($event, $tmpl) use ($view) {
            if ($this->exists($name = $tmpl->getName())) {
                $tmpl->setResult($view->render(
                    'position.'.($tmpl->getParameter('renderer') ?: 'default'),
                    ['widgets' => $this->positions[$name], 'options' => $tmpl->getParameters()]
                ));
            }
        }, 10);
    }

    /**
     * Set shortcut.
     *
     * @see add()
     */
    public function __invoke($widget)
    {
        $this->set($widget);
    }

    /**
     * Gets a position.
     *
     * @param  string $name
     * @return WidgetInterface[]
     */
    public function get($name)
    {
        return isset($this->positions[$name]) ? $this->positions[$name] : [];
    }

    /**
     * Adds a widget to a position.
     *
     * @param  WidgetInterface $widget
     * @param  string          $name
     * @return WidgetInterface[]
     */
    public function add(WidgetInterface $widget, $name = '')
    {
        $this->positions[$name ?: $widget->getPosition()][] = $widget;
    }

    /**
     * Sets the widgets for a position.
     *
     * @param string            $name
     * @param WidgetInterface[] $widgets
     */
    public function set($name, array $widgets = [])
    {
        $this->positions[$name] = $widgets;
    }

    /**
     * Checks if the position exists.
     *
     * @param  string $name
     * @return bool
     */
    public function exists($name)
    {
        return isset($this->positions[$name]) && !empty($this->positions[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'position';
    }
}
