<?php


require 'phar://' . __DIR__ . '/composer.phar/src/bootstrap.php';
require 'src/Application.php';

use Pagekit\Updater\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\StreamOutput;

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

    $output = new StreamOutput(fopen('php://output', 'w'));

    // parse request parameters
    if (isset($_GET['packages'])) {
        $packages = explode(' ', $_GET['packages']);
    }
    if (isset($_GET['remove'])) {
        $remove = isset($_GET['remove']);
    }

}

$config = require(__DIR__ . '/../config.php');

try {
    $updater = new Application($config, $output);
    $updater->run(compact('packages', 'remove'));
} catch (Exception $e) {
    $output->writeln($e->getMessage());
}
