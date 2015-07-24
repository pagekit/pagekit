<?php

$output = [];
$count  = count($widgets);

$output[] = '<div class="uk-grid uk-grid-width-medium-1-'.$count.'">';

foreach ($widgets as $widget) {

    $output[] = '<div>';
    $output[] =     '<div class="uk-panel">';
    $output[] =         $widget->get('show_title') ? '<h3>'.$widget->getTitle().'</h3>':'';
    $output[] =         $widget->get('result');
    $output[] =     '</div>';
    $output[] = '</div>';

}

$output[] = '</div>';

echo implode("\n", $output);
