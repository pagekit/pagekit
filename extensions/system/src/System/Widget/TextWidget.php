<?php

namespace Pagekit\System\Widget;

use Pagekit\Framework\ApplicationAware;
use Pagekit\System\Event\ContentEvent;
use Pagekit\Widget\Model\TypeInterface;
use Pagekit\Widget\Model\WidgetInterface;

class TextWidget extends ApplicationAware implements TypeInterface
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
    public function getDescription()
    {
        return __('Text Widget');
    }

    /**
     * {@inheritdoc}
     */
    public function getInfo(WidgetInterface $widget)
    {
        return __('Text Widget');
    }

    /**
     * {@inheritdoc}
     */
    public function render(WidgetInterface $widget, $options = array())
    {
        $this('events')->trigger('content.plugins', $event = new ContentEvent($widget->get('content'), compact('widget')));

        return $this('view')->render('system/widgets/text/render.razr.php', array('widget' => $widget, 'options' => $options, 'content' => $event->getContent()));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return $this('view')->render('system/widgets/text/edit.razr.php', compact('widget'));
    }
}
