<?php

namespace Pagekit\Widget\Event;

use Pagekit\Widget\Entity\Widget;
use Symfony\Component\EventDispatcher\Event;

class WidgetEvent extends Event
{
    /**
     * @var Widget
     */
    protected $widget;

    public function __construct(Widget $widget)
    {
        $this->widget = $widget;
    }

    /**
     * @return Widget
     */
    public function getWidget()
    {
        return $this->widget;
    }
}
