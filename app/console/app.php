<?php

use Pagekit\Console\Application as Console;

require $path . '/autoload.php';

if (PHP_SAPI !== 'cli') {
    die();
}

$console = new Console($config, 'Pagekit Console');

// Register commands
$namespace = 'Pagekit\\Console\\Commands\\';
foreach (glob(__DIR__ . '/src/Commands/*Command.php') as $file) {
    $class = $namespace . basename($file, '.php');
    $console->add(new $class);
}

$console->run();