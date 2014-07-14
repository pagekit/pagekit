<?php

$widths = array(
    array('1-1'),
    array('1-2', '1-2'),
    array('1-3', '1-3', '1-3'),
    array('1-4', '1-4', '1-4', '1-4'),
    array('1-5', '1-5', '1-5', '1-5', '1-5'),
    array('1-6', '1-6', '1-6', '1-6', '1-6', '1-6')
);

$i      = 0;
$output = array();
$count  = count($value);
$width  = isset($widths[($count-1)]) ? $widths[($count-1)] : array_pad(array(), $count, '1-6');

foreach ($value as $widget) {

    $class     = $width[$i];

    $output[] = '<div class="uk-width-medium-'.$class.'">';
    $output[] =     '<div class="uk-panel">';
    $output[] =         $widget->getShowTitle() ? '<h3>'.$widget->getTitle().'</h3>':'';
    $output[] =         $options['provider']->render($widget, $options);
    $output[] =     '</div>';
    $output[] = '</div>';

    $i++;
}

echo implode("\n", $output);