<?php

use Pagekit\Application as App;
use Pagekit\Module\Loader\AutoLoader;
use Pagekit\Module\Loader\ConfigLoader;

$loader = require $path.'/autoload.php';

$app = new App($config);
$app['autoloader'] = $loader;

$app['module']->addPath([
    $path.'/app/modules/*/index.php',
    $path.'/extensions/*/index.php',
    $path.'/themes/*/index.php',
    __DIR__.'/index.php'
]);

$app['module']->addLoader(new AutoLoader($app['autoloader']));
$app['module']->addLoader(new ConfigLoader(require __DIR__.'/config.php'));
$app['module']->addLoader(new ConfigLoader(require $app['config.file']));
$app['module']->load('system');

$app->run();