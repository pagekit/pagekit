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
    public static function findActive($position)
    {
        static $widgets, $positions, $node, $active;

        if (null === $widgets) {
            $widgets = self::where(['status' => 1])->get();
            $positions = App::position()->all();
            $node = App::node()->id;
            $active = [];
        }

        if (!isset($positions[$position])) {
            return [];
        }

        if (!isset($active[$position])) {

            $active[$position] = [];

            foreach ($positions[$position]['assigned'] as $id) {

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
                $active[$position][] = $widget;
            }
        }

        return $active[$position];
    }
}
