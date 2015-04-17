<?php

namespace Pagekit\Widget\Controller;

use Pagekit\Application as App;
use Pagekit\Widget\Entity\Widget;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Access("system: manage widgets", admin=true)
 */
class WidgetsController
{
    /**
     * @Request({"type", "id": "int"})
     * @Response("widget:views/admin/edit.php", layout="app/system/modules/theme/templates/component.php")
     */
    public function editAction($type = '', $id = 0)
    {
        if (!$widget = Widget::find($id)) {

            if (!$type || $id) {
                throw new NotFoundHttpException;
            }

            $widget = new Widget;
            $widget->setType($type);
        }

        $module = App::module('system/widget');

        App::view()->script('widget.edit', 'widget:app/edit.js', ['vue-validator', 'uikit', 'site-tree']);

        return [
            'widget'   => $widget,
            'sections' => $module->getSections($widget->getType()),
            '$data'    => [
                'widget' => $widget,
                'types'  => array_values($module->getTypes()),
                'positions'  => array_values($module->getPositions())
            ]
        ];
    }
}
