<?php

foreach ($widgets as $widget) {

    if ($widget->type == 'widget.menu') {
        echo $widget->render(array_merge(['layout' => 'app/modules/menu/views/widgets/menu/nav.razr', 'classes' => 'uk-nav-offcanvas'], $options));
    } else {
        $view->render('alpha:views/renderer/position.panel.php', ['widgets' => [$widget]]);
    }
}
