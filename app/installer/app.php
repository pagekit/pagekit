<?php

use Pagekit\Application as App;
use Pagekit\Installer\Controller\UpdateController;
use Pagekit\Module\Loader\AutoLoader;
use Pagekit\Module\Loader\ConfigLoader;

$loader = require $path.'/autoload.php';
$requirements = require __DIR__.'/requirements.php';

if ($failed = $requirements->getFailedRequirements()) {
    require __DIR__.'/views/requirements.php';
    exit;
}

if (isset($_SERVER['HTTP_X_UPDATE_MODE'])) {

    $config = array_replace(require $config['config.file'], $config);

    if (PHP_SAPI != 'cli') {
        $request = array_replace(['file' => '', 'token' => ''], $_GET);
    } else {
        $request = ['file' => isset($argv[1]) ? $argv[1] : ''];
    }

    UpdateController::updateAction($config, $request);
    exit;
}

$app = new App($config);
$app['autoloader'] = $loader;

$app['module']->addPath([
    $path.'/app/modules/*/index.php',
    $path.'/app/system/index.php',
    __DIR__.'/index.php'
]);

$app['module']->addLoader(new AutoLoader($app['autoloader']));
$app['module']->addLoader(new ConfigLoader(require $path.'/app/system/config.php'));
$app['module']->addLoader(new ConfigLoader(require __DIR__.'/config.php'));
$app['module']->load('installer');

$app->run();