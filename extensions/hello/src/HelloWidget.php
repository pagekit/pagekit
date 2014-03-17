<?php

namespace Pagekit\Hello;

use Pagekit\System\Widget\Model\TypeInterface;
use Pagekit\System\Widget\Model\WidgetInterface;

class HelloWidget implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'widget.hello';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Hello Widget!');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return __('Hello Demo Widget');
    }

    /**
     * {@inheritdoc}
     */
    public function getInfo(WidgetInterface $widget)
    {
        return __('Hello Demo Widget');
    }

    /**
     * {@inheritdoc}
     */
    public function render(WidgetInterface $widget, $options = array())
    {
        return __('Hello Widget!');
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return __('Hello Widget Form.');
    }
}
