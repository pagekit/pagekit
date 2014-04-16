<?php

namespace Pagekit\Widget\Event;

use Pagekit\Framework\Event\Event;
use Pagekit\Widget\Entity\Widget;

class WidgetEvent extends Event
{
    /**
     * @var Widget
     */
    protected $widget;

    public function __construct(Widget $theme)
    {
        $this->widget = $theme;
    }

    /**
     * @return Widget
     */
    public function getWidget()
    {
        return $this->widget;
    }
}
