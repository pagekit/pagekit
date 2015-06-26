<?php

foreach ($widgets as $widget) {
    if ($widget->getType() == 'widget.menu') {
        echo $widget->render(array_merge(['layout' => 'app/modules/menu/views/widgets/menu/navbar.razr'], $options));
    } else {
        echo $widget->get('result');
    }
}
