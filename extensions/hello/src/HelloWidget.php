<?php

namespace Pagekit\Hello;

use Pagekit\Application as App;
use Pagekit\Widget\Model\TypeInterface;
use Pagekit\Widget\Model\WidgetInterface;

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
    public function getDescription(WidgetInterface $widget = null)
    {
        return __('Hello Demo Widget');
    }

    /**
     * {@inheritdoc}
     */
    public function render(WidgetInterface $widget, $options = [])
    {
        $user = App::user();

        return App::tmpl('extensions/hello/views/widget.razr', compact('user'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return __('Hello Widget Form.');
    }
}
