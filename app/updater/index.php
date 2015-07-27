<?php

$configFile = __DIR__ . '/../../packages.json';

require 'phar://' . __DIR__ . '/composer.phar/src/bootstrap.php';

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\StreamOutput;

if (PHP_SAPI === 'cli') {
    $output = new ConsoleOutput();
    $opts = getopt('p:v:');
} else {
    $opts = $_REQUEST;
    $output = new StreamOutput(fopen('php://output', 'w'));
}

var_dump($opts);

$config = require(__DIR__ . '/../config.php');
$memory = trim(ini_get('memory_limit'));

// set environment
putenv('COMPOSER_HOME=' . $config['values']['path.temp']);
putenv('COMPOSER_CACHE_DIR=' . $config['values']['path.cache'] . '/composer');
putenv('COMPOSER_VENDOR_DIR=' . $config['values']['path'] . '/vendor');

// set memory limit, if < 512M
if ($memory != -1 && memoryInBytes($memory) < 512 * 1024 * 1024) {
    @ini_set('memory_limit', '512M');
}

if (isset($opts['p'])) {
    $config = file_exists($configFile) ? json_decode(file_get_contents($configFile), true) : [];
    if (!isset($config['require'])) {
        $config['require'] = [];
    }

    if (isset($opts['v']) && preg_match('/^[\w\d\-_]+\/[\w\d\-_]+\z/', $opts['p'])) {
        $config['require'][$opts['p']] = $opts['v'];
        $name = $opts['p'];

    } else {
        if (!($path = realpath($opts['p']))) {
            die('Can not find package.');
        }
        if (!($packageConfig = file_get_contents($path . '/composer.json'))) {
            die('No composer.json');
        }
        if (!isset($config['packages'])) {
            $config['packages'] = [];
        }

        $packagesConfig = json_decode($configFile, true);
        $name = $packagesConfig['name'];
        $config['packages'][] = $packagesConfig;
        $config['require'][$name] = $packagesConfig['version'];
    }

    file_put_contents($configFile, json_encode($config));
}

$params = ['update', '--prefer-dist'];
if (isset($name)) {
    $params['packages'] = [$name];
}

$input = new ArrayInput($params);

chdir(__DIR__ . '/../..');
(new Application())->run($input, $output);

function memoryInBytes($value)
{
    $unit = strtolower(substr($value, -1, 1));
    $value = (int)$value;

    switch ($unit) {
        case 'g':
            $value *= 1024;
        // no break (cumulative multiplier)
        case 'm':
            $value *= 1024;
        // no break (cumulative multiplier)
        case 'k':
            $value *= 1024;
    }

    return $value;
}