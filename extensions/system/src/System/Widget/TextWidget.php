<?php

namespace Pagekit\System\Widget;

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
    public function render(WidgetInterface $widget, $options = [])
    {
        return $this['content']->applyPlugins($widget->get('content'), ['widget' => $widget, 'markdown' => $widget->get('markdown')]);
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return $this['view']->render('extension://system/views/widgets/text/edit.razr', compact('widget'));
    }
}
