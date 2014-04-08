<?php

namespace Pagekit\Widget\Event;

use Pagekit\Widget\Entity\Widget;

class WidgetCopyEvent extends WidgetEvent
{
    /**
     * @var Widget
     */
    protected $copy;

    public function __construct(Widget $original, Widget $copy)
    {
        parent::__construct($original);

        $this->copy = $copy;
    }

    /**
     * @return Widget
     */
    public function getCopy()
    {
        return $this->copy;
    }
}
