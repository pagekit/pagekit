<?php

namespace Pagekit\System\Widget;

use Pagekit\Component\View\Widget\Model\TypeInterface;
use Pagekit\Component\View\Widget\Model\WidgetInterface;
use Pagekit\Framework\ApplicationAware;
use Pagekit\System\Event\ContentEvent;

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
