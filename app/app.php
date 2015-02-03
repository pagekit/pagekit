<?php

use Pagekit\Application as App;

$loader  = require __DIR__.'/autoload.php';
$config  = require __DIR__.'/config.php';
$modules = ['framework', 'system/core', 'system/cache', 'system/option', 'system/profiler', 'system/templating', 'system/locale'];

$app = new App($config);
$app['autoloader'] = $loader;

date_default_timezone_set('UTC');

$app['module']->addPath([$app['path.vendor'].'/pagekit/framework/*/module.php', $app['path.extensions'].'/*/extension.php', $app['path.themes'].'/*/theme.php']);

if (!$app['config.file']) {

    $requirements = require __DIR__.'/requirements.php';

    if ($failed = $requirements->getFailedRequirements()) {
        require $app['path.extensions'].'/installer/views/requirements.php';
        exit;
    }

    $app['config']->load(__DIR__.'/config/install.php');
    $app['module']->setConfig($app['config']->getValues());
    $app['module']->load($modules);
    $app['modules'] = ['installer'];

} else {

    $app['module']->setConfig($app['config']->getValues());
    $app['module']->load($modules);
    $app['modules'] = array_merge($app['option']->get('system:extensions', []), ['system']);

}

return $app;