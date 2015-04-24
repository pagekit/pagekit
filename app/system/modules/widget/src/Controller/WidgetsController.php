<?php

namespace Pagekit\Widget\Controller;

use Pagekit\Application as App;
use Pagekit\Kernel\Exception\NotFoundException;
use Pagekit\Widget\Entity\Widget;

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
                throw new NotFoundException;
            }

            $widget = Widget::create(['type' => $type]);
        }

        $module = App::module('system/widget');

        App::view()->script('widget.edit', 'widget:app/edit.js', ['vue-validator', 'uikit', 'site-tree']);

        $position = '';
        foreach ($module->config('widget.positions') as $pos => $widgets) {
            if (in_array($widget->getId(), $widgets)) {
                $position = $pos;
                break;
            }
        }

        return [
            'widget'   => $widget,
            'sections' => $module->getSections($widget->getType()),
            '$data'    => [
                'widget'    => $widget,
                'position'  => $position,
                'config'    => $module->getWidgetConfig($widget->getId()) ?: new \stdClass(),
                'types'     => array_values($module->getTypes('site')),
                'positions' => array_values($module->getPositions())
            ]
        ];
    }
}
