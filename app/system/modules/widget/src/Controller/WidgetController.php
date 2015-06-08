<?php

namespace Pagekit\Widget\Controller;

use Pagekit\Application as App;

/**
 * @Access("system: manage widgets", admin=true)
 */
class WidgetController
{
    protected $widgets;

    public function __construct()
    {
        $this->widgets = App::module('system/widget');
    }

    public function indexAction()
    {
        return [
            '$view' => [
                'title' => __('Widgets'),
                'name'  => 'widget:views/admin/index.php'
            ]
        ];
    }
}
