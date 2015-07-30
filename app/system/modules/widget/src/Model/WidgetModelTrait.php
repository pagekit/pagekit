<?php

namespace Pagekit\Widget\Model;

use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;

trait WidgetModelTrait
{
    use ModelTrait;

    /**
     * Gets active widgets.
     *
     * @param  string|null $position
     * @return Widget[]
     */
    public static function findActive($position = null)
    {
        static $widgets;

        if ($widgets === null) {

            $node    = App::node()->id;
            $widgets = self::where(['status' => 1])->get();

            foreach (App::theme()->getPositions() as $pos) {
                foreach ($pos['assigned'] as $id) {

                    if (!isset($widgets[$id])
                        or !$widget = $widgets[$id]
                        or !$widget->hasAccess(App::user())
                        or ($nodes = $widget->nodes and !in_array($node, $nodes))
                        or !$type = App::widget()->getType($widget->type)
                        or !$result = $type->render($widget)
                    ) {
                        continue;
                    }

                    $widget->set('result', $result);
                    $widgets[$pos['name']][] = $widget;
                }
            }
        }

        if ($position === null) {
            return $widgets;
        }

        return isset($widgets[$position]) ? $widgets[$position] : [];
    }
}
