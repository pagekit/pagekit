<?php

namespace Pagekit\System\Position;

use Pagekit\Component\View\View;
use Pagekit\System\Widget\WidgetProvider;

class LayoutPositionRenderer implements PositionRendererInterface
{
    /**
     * @var View
     */
    protected $view;

    /**
     * @var string
     */
    protected $layout;

    /**
     * Constructor.
     */
    public function __construct(View $view, $layout)
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
