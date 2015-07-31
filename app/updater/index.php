<?php

require 'phar://' . __DIR__ . '/composer.phar/src/bootstrap.php';
require 'src/Updater.php';
require 'src/Output.php';

use Pagekit\Updater\Updater;
use Pagekit\Updater\Output;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\BufferedOutput;

if (PHP_SAPI === 'cli') {
    $output = new ConsoleOutput();

    // parse CLI input
    $opts = getopt('p:r::', ['packages:', 'remove::']);

    if (isset($opts['p']) || isset($opts['packages'])) {
        $packages = explode(' ', isset($opts['p']) ? $opts['p'] : $opts['package']);
    }
    if (isset($opts['r']) || isset($opts['remove'])) {
        $remove = isset($opts['r']) ?: isset($opts['remove']);
    }
} else {
    $output = new Output();

    // parse request parameters
    if (isset($_GET['packages'])) {
        $packages = explode(' ', $_GET['packages']);
    }
    if (isset($_GET['remove'])) {
        $remove = isset($_GET['remove']);
    }

    register_shutdown_function(function () use ($output) {
        echo json_encode([
            'status' => ($error = $output->getErrorOutput()->fetch()) ? 'failure' : 'success',
            'message' => $error ?: $output->fetch()
        ]);
    });
}

$config = require(__DIR__ . '/../config.php');

try {
    $updater = new Updater($config, $output);
    $updater->run(compact('packages', 'remove'));
} catch (Exception $e) {
    $output->getErrorOutput()->writeln($e->getMessage());
}
