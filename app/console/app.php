<?php

use Pagekit\Application as App;
use Pagekit\Application\Console\Application as Console;
use Pagekit\Module\Loader\AutoLoader;
use Pagekit\Module\Loader\ConfigLoader;

$loader = require $path.'/autoload.php';

$app = new App($config);
$app['autoloader'] = $loader;

$app['module']->addPath([
    $path.'/app/system/index.php',
    $path.'/app/modules/*/index.php',
    $path.'/app/installer/index.php',
    $path.'/packages/*/*/index.php',
    __DIR__.'/index.php'
]);

$app['module']->addLoader(new AutoLoader($app['autoloader']));
$app['module']->addLoader(new ConfigLoader(require $path.'/app/system/config.php'));
$app['module']->addLoader(new ConfigLoader(require $app['config.file']));

$app['module']->load('system');
$app['module']->load('console');

$console = new Console($app, 'Pagekit');
$console->run();