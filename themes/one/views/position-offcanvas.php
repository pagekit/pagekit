<?php

foreach ($widgets as $widget) {

    if ($widget->getType() == 'widget.menu') {
        echo $widget->render(array_merge(['layout' => 'app/modules/menu/views/widgets/menu/nav.razr', 'classes' => 'uk-nav-offcanvas'], $options));
    } else {
        echo $view->render('theme:views/position-panel.php', ['widgets' => [$widget]]);
    }
}
