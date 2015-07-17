<?php

namespace Theme;

$config  = $theme->config;
$classes = [];

/*
* Layouts
*/

$width = 60;
foreach ($sidebars = $config['sidebars'] as $name => $sidebar) {

    if (!$view->position()->exists($name)) {
        unset($sidebars[$name]);
        continue;
    }

    $width -= @$sidebar['width'];
}

foreach (($sidebars + array('main' => array('width' => $width))) as $name => $column) {
    $classes["layout.$name"][] = sprintf('tm-%s uk-width-medium-%s%s', $name, GridHelper::getFraction(@$column['width']), @$column['first'] ? ' uk-flex-order-first-medium' : '');
}

if ($count = count($sidebars)) {
    $classes['body'][] = 'tm-sidebars-'.$count;
}

/*
* Grid
*/

// $displays = array('small', 'medium', 'large');
foreach ($config['grid'] as $name => $position) {

    $grid = array("tm-{$name} uk-grid");

    if ($position['divider']) {
        $grid[] = 'uk-grid-divider';
    }

//    $widgets = $this['widgets']->load($name);
//
//    foreach ($displays as $display) {
//        if (!array_filter($widgets, function ($widget) use ($config, $display) {
//            return (bool) $config->get("widgets.{$widget->id}.display.{$display}", true);
//        })
//        ) {
//            $grid[] = "uk-hidden-{$display}";
//        }
//    }
    $classes["grid.$name"] = $grid;
}

/*
* Blocks
*/

$styles = [];

foreach ($config['blocks'] as $name => $position) {

    $block = [];

    if ($position['background'] && !$position['image']) {
        $block[] = 'uk-block-'.$position['background'];
    }

    if ($position['contrast']) {
        $block[] = 'uk-contrast';
    }

    if ($position['height']) {
        $block[] = 'tm-block-height uk-flex uk-flex-middle';
    }

    if ($position['padding']) {
        $block[] = ($position['padding'] == 'large') ? 'uk-block-large' : '';
        $block[] = ($position['padding'] == 'none') ? 'uk-padding-vertical-remove' : '';
    }

    $styles["block.$name"] = '';
    if ($position['image']) {
        $styles["block.$name"] = 'style="background-image: url(\''.$position['image'].'\');"';
        $block[]               = 'uk-cover-background';
    }

    $classes["block.$name"] = $block;

}

/*
 * Flatten classes
 */

$classes = array_map(function($array) { return implode(' ', $array); }, $classes);

/*
 * Helper
 */

class GridHelper
{
    public static function gcf($a, $b = 60)
    {
        return (int) ($b > 0 ? self::gcf($b, $a % $b) : $a);
    }

    public static function getFraction($nominator, $divider = 60)
    {
        $factor = self::gcf($nominator, $divider);
        return $nominator / $factor.'-'.$divider / $factor;
    }
}
