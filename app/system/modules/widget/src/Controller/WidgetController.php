<?php

namespace Pagekit\Widget\Controller;

use Pagekit\Application as App;
use Pagekit\Site\Model\Node;
use Pagekit\User\Model\Role;
use Pagekit\Widget\Model\Widget;

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
                'theme' => App::theme(),
                'types' => App::widget()->getTypes()
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
                'theme' => App::theme(),
                'config' => [
                    'menus' => App::menu(),
                    'nodes' => array_values(Node::query()->get()),
                    'roles' => array_values(Role::findAll()),
                    'types' => array_values(App::widget()->getTypes())
                ]
            ]
        ];
    }
}
