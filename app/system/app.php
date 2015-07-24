<?php

use Pagekit\Module\Loader\ConfigLoader;

$app['module']->addPath([
    $app['path.extensions'].'/*/index.php',
    $app['path.themes'].'/*/index.php'
]);

$app['module']->addLoader(new ConfigLoader(require $app['config.file']));
$app['module']->load('system');

$app->run();