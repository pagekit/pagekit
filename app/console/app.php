<?php

use Pagekit\Application as App;
use Pagekit\Application\Console\Application as Console;
use Pagekit\Module\Loader\AutoLoader;
use Pagekit\Module\Loader\ConfigLoader;
use Pagekit\Event\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Console\ConsoleEvents;

$loader = require $path.'/autoload.php';

$app = new App($config);
$app['autoloader'] = $loader;

$app['module']->register([
    'packages/*/*/index.php',
    'app/modules/*/index.php',
    'app/installer/index.php',
    'app/system/index.php',
    'app/console/index.php'
], $path);

$app['module']->addLoader(new AutoLoader($app['autoloader']));
$app['module']->addLoader(new ConfigLoader(require $path.'/app/system/config.php'));

if ($app['config.file']) {
    $app['module']->addLoader(new ConfigLoader(require $app['config.file']));
    $app['module']->load('system');
}
$app['module']->load('console');

// Listen to console termination and terminate main application
$dispatcher = new EventDispatcher();
$dispatcher->addListener(ConsoleEvents::TERMINATE, function() use ($app) {
    $app->trigger(new Event('terminate'));
});

$console = new Console($app, 'Pagekit', $app->version());
$console->setDispatcher($dispatcher);
$console->run();
