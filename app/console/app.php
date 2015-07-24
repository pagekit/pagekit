<?php

use Pagekit\Application\Console\Application as Console;

$app['module']->addPath(__DIR__.'/index.php');
$app['module']->load('console');

$console = new Console($app, 'Pagekit');
$console->run();