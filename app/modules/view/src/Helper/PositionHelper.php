<?php

namespace Pagekit\View\Helper;

use Pagekit\View\View;
use Pagekit\Widget\Model\WidgetInterface;

class PositionHelper implements HelperInterface
{
    /**
     * @var array
     */
    protected $positions = [];

    /**
     * Constructor.
     *
     * @param View $view
     */
    public function __construct(View $view)
    {
        $view->on('render', function ($event, $view) {
            if ($this->exists($name = $event->getTemplate())) {
                $event->setResult($view->render(
                    'position.'.($event->getParameter('renderer') ?: 'default'),
                    ['widgets' => $this->positions[$name], 'options' => $event->getParameters()]
                ));
            }
        }, 10);
    }

    /**
     * Set shortcut.
     *
     * @see get()
     * @see add()
     */
    public function __invoke($name, WidgetInterface $widget = null)
    {
        if (null === $widget) {
            return $this->get($name);
        }

        $this->add($name, $widget);
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
     * @param  string          $name
     * @param  WidgetInterface $widget
     */
    public function add($name, WidgetInterface $widget)
    {
        $this->positions[$name][] = $widget;
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
