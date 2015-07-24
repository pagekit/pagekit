<?php

use Pagekit\Application\Console\Application as Console;

$app['module']->addPath(__DIR__.'/module.php');
$app['module']->load('console');

$console = new Console($app, 'Pagekit');
$console->run();