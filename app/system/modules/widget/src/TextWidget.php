<?php

namespace Pagekit\Widget;

use Pagekit\Application as App;
use Pagekit\Widget\Model\Type;
use Pagekit\Widget\Model\WidgetInterface;

class TextWidget extends Type
{
    /**
     * {@inheritdoc}
     */
    public function render(WidgetInterface $widget, $options = [])
    {
        return App::content()->applyPlugins($widget->get('content'), ['widget' => $widget, 'markdown' => $widget->get('markdown')]);
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return App::view('widget:views/widgets/text/edit.razr', compact('widget'));
    }
}
