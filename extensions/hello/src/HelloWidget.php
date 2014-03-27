<?php

namespace Pagekit\Hello;

use Pagekit\Framework\ApplicationAware;
use Pagekit\Widget\Model\TypeInterface;
use Pagekit\Widget\Model\WidgetInterface;

class HelloWidget extends ApplicationAware implements TypeInterface
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
        return $this('view')->render('hello/widget.razr.php');
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return __('Hello Widget Form.');
    }
}
