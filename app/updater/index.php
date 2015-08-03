<?php

require 'phar://' . __DIR__ . '/composer.phar/src/bootstrap.php';

require 'src/OutputFilter.php';
require 'src/Updater.php';

use Symfony\Component\Console\Output\StreamOutput;

use Pagekit\Updater\OutputFilter;
use Pagekit\Updater\Updater;

$config = require(__DIR__ . '/../config.php');

if (PHP_SAPI === 'cli') {

    // parse CLI input
    $opts = getopt('p:r::', ['packages:', 'remove::']);

    $output = new OutputFilter(fopen('php://stderr', 'w'));

} else {

    // parse request parameters
    $opts = $_GET;

    $output = new OutputFilter(fopen('php://output', 'w'));

    ob_start();
    register_shutdown_function(function () use ($output) {

        $messages = ob_get_contents();
        ob_end_clean();

        echo json_encode([
            'status' => empty($output->getError()) ? 'success' : 'failure',
            'message' => $output->getError() ?: $messages
        ]);

    });

}

if (isset($opts['p']) || isset($opts['packages'])) {
    $packages = explode(' ', isset($opts['p']) ? $opts['p'] : $opts['packages']);
}

if (isset($opts['r']) || isset($opts['remove'])) {
    $remove = isset($opts['r']) ?: isset($opts['remove']);
}

try {
    $updater = new Updater($config, $output);
    $updater->run(compact('packages', 'remove'));
} catch (Exception $e) {
    $output->getErrorOutput()->writeln($e->getMessage());
}
