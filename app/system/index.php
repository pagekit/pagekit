<?php

use Pagekit\Module\Loader\ConfigLoader;

$app['module']->addPath([
    $app['path.extensions'].'/*/extension.php',
    $app['path.themes'].'/*/theme.php'
]);

$app['module']->addLoader(new ConfigLoader(require $app['config.file']));
$app['module']->load('system');

$app->run();