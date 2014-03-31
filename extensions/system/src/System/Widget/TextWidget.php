<?php

namespace Pagekit\System\Widget;

use Pagekit\Content\Event\ContentEvent;
use Pagekit\Widget\Model\Type;
use Pagekit\Widget\Model\WidgetInterface;

class TextWidget extends Type
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'widget.text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Text');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(WidgetInterface $widget = null)
    {
        return __('Text Widget');
    }

    /**
     * {@inheritdoc}
     */
    public function render(WidgetInterface $widget, $options = array())
    {
        $this('events')->trigger('content.plugins', $event = new ContentEvent($widget->get('content'), compact('widget')));

        return $event->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return $this('view')->render('system/widgets/text/edit.razr.php', compact('widget'));
    }
}
