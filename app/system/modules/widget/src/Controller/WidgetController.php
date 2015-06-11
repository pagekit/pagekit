<?php

namespace Pagekit\Widget\Controller;

use Pagekit\Application as App;
use Pagekit\Site\Entity\Node;
use Pagekit\Widget\Entity\Widget;

/**
 * @Access("system: manage widgets", admin=true)
 */
class WidgetController
{
    protected $site;
    protected $widgets;

    public function __construct()
    {
        $this->site = App::module('system/site');
        $this->widgets = App::module('system/widget');
    }

    public function indexAction()
    {
        return [
            '$view' => [
                'title' => __('Widgets'),
                'name'  => 'widget:views/admin/index.php'
            ],
            '$data' => [
                'config' => [
                    'types'     => array_values($this->widgets->getTypes()),
                    'positions' => array_values($this->widgets->getPositions())
                ]
            ]
        ];
    }

    /**
     * @Request({"id": "int", "type": "string"})
     */
    public function editAction($id = 0, $type = null)
    {
        $widget = Widget::find($id);

        if (!$widget) {
            $widget = new Widget();
            $widget->setType($type);
        }

        return [
            '$view' => [
                'title' => __('Widgets'),
                'name'  => 'widget:views/admin/edit.php'
            ],
            '$data' => [
                'widget' => $widget,
                'config' => [
                    'menus'     => $this->site->getMenus(),
                    'nodes'     => array_values(Node::query()->get()),
                    'types'     => array_values($this->widgets->getTypes()),
                    'positions' => array_values($this->widgets->getPositions())
                ]
            ]
        ];
    }
}
