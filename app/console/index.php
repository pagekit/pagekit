<?php

use Pagekit\Application\Console\Application as Console;

$console = new Console($app, 'Pagekit', $app['version']);
$console->run();