<?php

    if ($widget->position == 'navbar') {

        echo $view->render('alpha:views/menu/navbar.php', compact('widget', 'root'));

    } else {

        echo $view->render('alpha:views/menu/default.php', compact('widget', 'root'));

    }
