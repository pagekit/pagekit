<?php

if (version_compare($ver = PHP_VERSION, $req = '5.5.9', '<')) {
    exit(sprintf('You are running PHP %s, but Pagekit needs at least <strong>PHP %s</strong> to run.', $ver, $req));
}

if (PHP_SAPI == 'cli-server' && is_file(__DIR__.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

if (!isset($_SERVER['HTTP_MOD_REWRITE'])) {
    $_SERVER['HTTP_MOD_REWRITE'] = 'Off';
}

date_default_timezone_set('UTC');

$env = 'system';
$path = __DIR__;
$config = array(
    'path'          => $path,
    'path.packages' => $path.'/packages',
    'path.storage'  => $path.'/storage',
    'path.temp'     => $path.'/tmp/temp',
    'path.cache'    => $path.'/tmp/cache',
    'path.logs'     => $path.'/tmp/logs',
    'path.vendor'   => $path.'/vendor',
    'path.artifact' => $path.'/tmp/packages',
    'config.file'   => realpath($path.'/config.php'),
    'system.api'    => 'https://pagekit.com'
);

if (!$config['config.file']) {
    $env = 'installer';
}

if (PHP_SAPI == 'cli') {
    $env = 'console';
}

require_once "$path/app/$env/app.php";