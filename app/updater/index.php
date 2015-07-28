<?php

$configFile = __DIR__ . '/../../packages.json';

require 'phar://' . __DIR__ . '/composer.phar/src/bootstrap.php';

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\StreamOutput;

if (PHP_SAPI === 'cli') {

    $output = new ConsoleOutput();

    // parse CLI Input
    $opts = getopt('p:c:r::', ['package:', 'constraint:', 'remove::']);

    if (isset($opts['p']) || isset($opts['package'])) {
        $package = isset($opts['p']) ? $opts['p'] : $opts['package'];
    }
    if (isset($opts['v']) || isset($opts['version'])) {
        $version = isset($opts['v']) ? $opts['v'] : $opts['version'];
    }
    if (isset($opts['r']) || isset($opts['remove'])) {
        $remove = isset($opts['r']) ?: isset($opts['remove']);
    }

} else {

    $output = new StreamOutput(fopen('php://output', 'w'));

    // parse request parameters
    if (isset($_GET['package'])) {
        $package = $_GET['package'];
    }
    if (isset($_GET['version'])) {
        $version = $_GET['version'];
    }
    if (isset($_GET['remove'])) {
        $remove = isset($_GET['remove']);
    }

}

// set environment
$config = require(__DIR__ . '/../config.php');
putenv('COMPOSER_HOME=' . $config['values']['path.temp']);
putenv('COMPOSER_CACHE_DIR=' . $config['values']['path.cache'] . '/composer');
putenv('COMPOSER_VENDOR_DIR=' . $config['values']['path'] . '/vendor');

// set memory limit, if < 512M
$memory = trim(ini_get('memory_limit'));
if ($memory != -1 && memoryInBytes($memory) < 512 * 1024 * 1024) {
    @ini_set('memory_limit', '512M');
}

if (isset($package) && !isset($remove)) {
    $config = file_exists($configFile) ? json_decode(file_get_contents($configFile), true) : [];

    if (!isset($config['require'])) {
        $config['require'] = [];
    }
    if (preg_match('/^[\w\d\-_]+\/[\w\d\-_]+\z/', $package)) {

        // install from Pagekit repository
        $config['require'][$package] = isset($version) ? $version : '*';
        $name = $package;

    } else {
        if (!($path = realpath($package))) {
            die('Can not find package.');
        }
        if (!($packageConfig = file_get_contents($path . '/composer.json'))) {
            die('No composer.json');
        }
        if (!isset($config['packages'])) {
            $config['packages'] = [];
        }

        // install from uploaded package
        $packagesConfig = json_decode($configFile, true);
        $name = $packagesConfig['name'];
        $config['packages'][$name] = $packagesConfig;
        $config['require'][$name] = $packagesConfig['version'];
    }

    file_put_contents($configFile, json_encode($config));
}

if (isset($package) && isset($remove)) {
    $config = file_exists($configFile) ? json_decode(file_get_contents($configFile), true) : [];

    // uninstall package
    unset($config['require'][$package]);
    unset($config['packages'][$package]);
    $name = $opts['p'];

    file_put_contents($configFile, json_encode($config));
}

// execute Composer update
$params = ['update', '--prefer-dist'];
if (isset($name)) {
    $params['packages'] = [$name];
}
$input = new ArrayInput($params);

chdir(__DIR__ . '/../..');
(new Application())->run($input, $output);

/*
 * Helper
 *
 */

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