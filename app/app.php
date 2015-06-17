<?php

use Pagekit\Application as App;
use Pagekit\Module\Loader\ArrayLoader;
use Pagekit\Module\Loader\AutoLoader;

$loader = require __DIR__.'/autoload.php';
$config = require __DIR__.'/config.php';

$app = new App($config['values']);
$app['autoloader'] = $loader;

date_default_timezone_set('UTC');

$app['module']->addPath([
    __DIR__.'/modules/*/module.php',
    __DIR__.'/installer/module.php',
    __DIR__.'/system/module.php',
    $app['path.extensions'].'/*/extension.php',
    $app['path.themes'].'/*/theme.php'
]);

$app['module']->addLoader(new AutoLoader($loader));
$app['module']->addLoader(new ArrayLoader($config));

if (!$app['config.file']) {

    $requirements = require __DIR__.'/installer/requirements.php';

    if ($failed = $requirements->getFailedRequirements()) {
        require __DIR__.'/installer/views/requirements.php';
        exit;
    }

    $app['module']->addLoader(new ArrayLoader(require __DIR__.'/installer/config.php'));
    $app['module']->load('installer');

} else {

    $app['module']->addLoader(new ArrayLoader(require $app['config.file']));
    $app['module']->load('system');

}

return $app;
