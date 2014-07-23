<?php

$widths = [
    ['1-1'],
    ['1-2', '1-2'],
    ['1-3', '1-3', '1-3'],
    ['1-4', '1-4', '1-4', '1-4'],
    ['1-5', '1-5', '1-5', '1-5', '1-5'],
    ['1-6', '1-6', '1-6', '1-6', '1-6', '1-6']
];

$i      = 0;
$output = [];
$count  = count($value);
$width  = isset($widths[($count-1)]) ? $widths[($count-1)] : array_pad([], $count, '1-6');

foreach ($value as $widget) {

    $class     = $width[$i];

    $output[] = '<div class="uk-width-medium-'.$class.'">';
    $output[] =     '<div class="uk-panel">';
    $output[] =         $widget->getShowTitle() ? '<h3>'.$widget->getTitle().'</h3>':'';
    $output[] =         $widget->render($options);
    $output[] =     '</div>';
    $output[] = '</div>';

    $i++;
}

echo implode("\n", $output);