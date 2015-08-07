<?php

require 'phar://' . __DIR__ . '/composer.phar/src/bootstrap.php';
require dirname(__DIR__) . '/autoload.php';

use Pagekit\Console\Application as Console;
use Pagekit\Console\OutputFilter;
use Pagekit\Console\ParameterVerifier;
use Composer\Console\HtmlOutputFormatter;
use Symfony\Component\Console\Input\ArrayInput;

if (version_compare($ver = PHP_VERSION, $req = '5.4.0', '<')) {
    exit(sprintf('You are running PHP %s, but Pagekit needs at least <strong>PHP %s</strong> to run.', $ver, $req));
}

date_default_timezone_set('UTC');

$config = require dirname(__DIR__) . '/config.php';

if (PHP_SAPI === 'cli') {
    $input = null;
    $output = new OutputFilter(fopen('php://stdout', 'w'));
} else {
    if (!$config['values']['config.file']) {
        exit('Pagekit needs to be installed before accessing the console over the web.');
    }

    // Check parameter integrity
    $verifier = new ParameterVerifier(require $config['values']['config.file']);
    if (!$verifier->verify($_GET)) {
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
    $output = new OutputFilter(fopen('php://output', 'w'));
    $output->setFormatter(new HtmlOutputFormatter());

    header('Content-type: text/html; charset=utf-8');

    ob_implicit_flush(true);
    ob_end_flush();

    register_shutdown_function(function () use ($output) {
        // TODO: Send status
    });
}

$console = new Console($config['values'], 'Pagekit Console');

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


