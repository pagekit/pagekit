<?php

foreach ($widgets as $widget) {

    if ($widget->getType() == 'widget.menu') {
        echo $widget->render(array_merge(['layout' => 'app/modules/menu/views/widgets/menu/nav.razr', 'classes' => 'uk-nav-offcanvas'], $options));
    } else {
        $view->render('alpha:views/position.panel.php', ['widgets' => [$widget]]);
    }
}
