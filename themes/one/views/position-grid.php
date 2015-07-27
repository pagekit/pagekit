<?php

$output = [];
$count  = count($widgets);

foreach ($widgets as $widget) {

    $output[] = '<div class="uk-width-medium-1-'.$count.'">';
    $output[] =     '<div class="uk-panel">';
    $output[] =         !$widget->get('title_hide') ? '<h3>'.$widget->getTitle().'</h3>' : '';
    $output[] =         $widget->get('result');
    $output[] =     '</div>';
    $output[] = '</div>';

}

echo implode("\n", $output);
