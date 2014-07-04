<?php

namespace Pagekit\Widget\PositionRenderer;

use Pagekit\Component\View\ViewInterface;
use Pagekit\Widget\WidgetProvider;

class LayoutPositionRenderer implements PositionRendererInterface
{
    /**
     * @var ViewInterface
     */
    protected $view;

    /**
     * @var string
     */
    protected $layout;

    /**
     * Constructor.
     */
    public function __construct(ViewInterface $view, $layout)
    {
        $this->view   = $view;
        $this->layout = $layout;
    }

    /**
     * {@inheritdoc}
     */
    public function render($position, WidgetProvider $provider, \ArrayObject $widgets, array $options = array())
    {
        return $this->view->render($this->layout, compact('position', 'provider', 'widgets', 'options'));
    }
}
