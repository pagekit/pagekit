<?php

use Pagekit\Application as App;
use Pagekit\Module\Loader\AutoLoader;
use Pagekit\Module\Loader\ConfigLoader;

$loader = require __DIR__ . '/../autoload.php';
$config = require __DIR__ . '/../config.php';

$app = new App($config['values']);
$app['autoloader'] = $loader;

$app['module']->addPath([
    __DIR__ . '/../modules/*/index.php',
    __DIR__ . '/../system/index.php',
]);

$app['module']->addLoader(new AutoLoader($loader));
$app['module']->addLoader(new ConfigLoader($config));
$app['module']->addLoader(new ConfigLoader(require $app['config.file']));

$app['module']->load('system');

return $app;