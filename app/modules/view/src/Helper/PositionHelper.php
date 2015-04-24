<?php

namespace Pagekit\View\Helper;

use Pagekit\View\ViewManager;
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
     * @param ViewManager $view
     */
    public function __construct(ViewManager $view)
    {
        $view->on('render', function ($event, $tmpl) use ($view) {
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
