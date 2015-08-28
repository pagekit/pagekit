<?php

use Pagekit\Console\Application as Console;
use Pagekit\Console\Output\FilterOutput;
use Pagekit\Console\Output\WebOutput;
use Pagekit\Console\UriVerifier;
use Symfony\Component\Console\Input\ArrayInput;

$loader = require $path . '/autoload.php';

if (PHP_SAPI === 'cli') {

    $input = null;
    $output = new FilterOutput(fopen('php://stdout', 'w'));

} else {

    if (!$config['config.file']) {
        exit('Pagekit needs to be installed before accessing the console over the web.');
    }

    // Check parameter integrity
    $verifier = new UriVerifier(require $config['config.file']);
    if (!$verifier->verify($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])) {
        http_response_code(401);
        exit('Invalid parameters.');
    }

    if (isset($_GET['command'])) {
        $command = $_GET['command'];
        $args = array_merge(compact('command'), $_GET);
    } else {
        $args = $_GET;
    }

    unset($args['expires'], $args['token']);

    $input = new ArrayInput($args);
    $output = new WebOutput(fopen('php://output', 'w'));
}

$console = new Console($config, 'Pagekit Console');

// Register commands
$namespace = 'Pagekit\\Console\\Commands\\';
foreach (glob(__DIR__ . '/src/Commands/*Command.php') as $file) {
    $class = $namespace . basename($file, '.php');
    $console->add(new $class);
}

try {
    $console->run($input, $output);
} catch (Exception $e) {
    $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
}


