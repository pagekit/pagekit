<?php

use Pagekit\Application as App;
use Pagekit\Module\Loader\AutoLoader;
use Pagekit\Module\Loader\ConfigLoader;

if (version_compare($ver = PHP_VERSION, $req = '5.4.0', '<')) {
    exit(sprintf('You are running PHP %s, but Pagekit needs at least <strong>PHP %s</strong> to run.', $ver, $req));
}

if (PHP_SAPI == 'cli-server' && is_file(__DIR__.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

if (!isset($_SERVER['HTTP_MOD_REWRITE'])) {
    $_SERVER['HTTP_MOD_REWRITE'] = 'On';
}

$loader = require __DIR__.'/autoload.php';
$config = require __DIR__.'/config.php';

$app = new App($config['values']);
$app['autoloader'] = $loader;

date_default_timezone_set('UTC');

$app['module']->addPath([
    __DIR__.'/modules/*/module.php',
    __DIR__.'/system/module.php'
]);

$app['module']->addLoader(new AutoLoader($loader));
$app['module']->addLoader(new ConfigLoader($config));

if ($app->inConsole()) {
    require __DIR__.'/console/index.php';
} elseif (!$app['config.file']) {
    require __DIR__.'/installer/index.php';
} else {
    require __DIR__.'/system/index.php';
}
