<?php

namespace Pagekit\Widget\Controller;

use Pagekit\Application as App;
use Pagekit\Site\Entity\Node;
use Pagekit\User\Entity\Role;
use Pagekit\Widget\Entity\Widget;

/**
 * @Access("system: manage widgets", admin=true)
 */
class WidgetController
{
    public function indexAction()
    {
        return [
            '$view' => [
                'title' => __('Widgets'),
                'name'  => 'widget:views/index.php'
            ],
            '$data' => [
                'config' => [
                    'types'     => App::widget()->getTypes(),
                    'positions' => array_values(App::widget()->getPositions())
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
            $widget->position = '';
        }

        return [
            '$view' => [
                'title' => __('Widgets'),
                'name'  => 'widget:views/edit.php'
            ],
            '$data' => [
                'widget' => $widget,
                'config' => [
                    'menus'     => App::menus(),
                    'nodes'     => array_values(Node::query()->get()),
                    'roles'     => array_values(Role::findAll()),
                    'types'     => array_values(App::widget()->getTypes()),
                    'positions' => App::widget()->getPositions()
                ]
            ]
        ];
    }
}
