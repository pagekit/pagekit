<?php

use Pagekit\Updater\Application;
use Pagekit\Updater\OutputFilter;

require 'phar://'.__DIR__ .'/composer.phar/src/bootstrap.php';
require 'src/Application.php';
require 'src/OutputFilter.php';

$config = require __DIR__.'/../config.php';

if (PHP_SAPI === 'cli') {

    // parse CLI input
    $options = getopt('p:r::', ['packages:', 'remove::']);
    $output  = new OutputFilter(fopen('php://stderr', 'w'));

} else {

    // parse request parameters
    $options = $_GET;
    $output  = new OutputFilter(fopen('php://output', 'w'));

    ob_start();

    register_shutdown_function(function () use ($output) {

        echo json_encode([
            'status' => $output->getError() ? 'success' : 'failure',
            'message' => $output->getError() ?: ob_get_clean()
        ]);

    });

}

if (isset($options['p']) || isset($options['packages'])) {
    $packages = explode(' ', isset($options['p']) ? $options['p'] : $options['packages']);
}

if (isset($options['r']) || isset($options['remove'])) {
    $remove = isset($options['r']) ?: isset($options['remove']);
}

try {

    $app = new Application($config, $output);
    $app->run(compact('packages', 'remove'));

} catch (Exception $e) {

    $output->writeln($e->getMessage());

}
