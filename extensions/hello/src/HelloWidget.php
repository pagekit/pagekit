<?php

namespace Pagekit\Hello;

use Pagekit\Application as App;
use Pagekit\Widget\Model\Type;
use Pagekit\Widget\Model\WidgetInterface;

class HelloWidget extends Type
{
    /**
     * Constructor.
     */
    public function __construct($type)
    {
        parent::__construct($type.'.hello', __('Hello Widget!'));
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

        return App::view('hello:views/widget.php', compact('user'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return __('Hello Widget Form.');
    }
}
