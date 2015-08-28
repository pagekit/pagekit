<?php

use Pagekit\Application as App;
use Pagekit\Installer\Controller\UpdateController;
use Pagekit\Module\Loader\AutoLoader;
use Pagekit\Module\Loader\ConfigLoader;

$requirements = require __DIR__ . '/requirements.php';

if ($failed = $requirements->getFailedRequirements()) {
    require __DIR__ . '/views/requirements.php';
    exit;
}

$loader = require $path . '/autoload.php';

if (isset($_SERVER['HTTP_X_UPDATE_MODE'])) {
    if (!isset($_GET['file'], $_SERVER['HTTP_X_SECURITY_TOKEN'])) {
        http_response_code(400);
        exit ('Invalid parameters.');
    }

    UpdateController::updateAction($config, $file, $token);
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