<?php

use Pagekit\Updater\Application;
use Pagekit\Updater\OutputFilter;
use Pagekit\Updater\TokenVerifier;

require 'phar://' . __DIR__ . '/composer.phar/src/bootstrap.php';
require 'src/Application.php';
require 'src/OutputFilter.php';
require 'src/TokenVerifier.php';

$config = require __DIR__ . '/../config.php';

if (PHP_SAPI === 'cli') {

    // parse CLI input
    $options = getopt('p:r::', ['packages:', 'remove::']);
    $output = new OutputFilter(fopen('php://stderr', 'w'));

} else {

    // verify access token
    $verifier = new TokenVerifier(require $config['values']['config.file']);

    if (!isset($_GET['token']) || !$verifier->verify($_GET['token'], array_diff_key(['token' => false], $_GET))) {
        http_response_code(401);
        die('No access permission.');
    }

    // parse request parameters
    $options = $_GET;
    $output = new OutputFilter(fopen('php://output', 'w'));

    ob_start();

    register_shutdown_function(function () use ($output) {

        echo json_encode([
            'status' => empty($output->getError()) ? 'success' : 'failure',
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

    $app = new Application($config['values'], $output);
    $app->run(compact('packages', 'remove'));

} catch (Exception $e) {

    $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

}
