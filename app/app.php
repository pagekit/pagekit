<?php

use Pagekit\Application as App;
use Pagekit\Module\Config\ConfigLoader;

$loader  = require __DIR__.'/autoload.php';
$config  = require __DIR__.'/config.php';

$app = new App($config['values']);
$app['autoloader'] = $loader;

date_default_timezone_set('UTC');

$app['module']->addPath([$app['path.vendor'].'/pagekit/framework/*/module.php', $app['path.extensions'].'/*/extension.php', $app['path.themes'].'/*/theme.php']);
$app['module']->addLoader(new ConfigLoader($config));

if (!$app['config.file']) {

    $requirements = require __DIR__.'/requirements.php';

    if ($failed = $requirements->getFailedRequirements()) {
        require $app['path.extensions'].'/installer/views/requirements.php';
        exit;
    }

    $app['config']->load(__DIR__.'/config/install.php');
    $app['module']->load('system/installer');

} else {

    $app['module']->load('system');

}

return $app;
