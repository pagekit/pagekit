<?php

use Pagekit\Component\Config\Config;

$values = array_map('realpath', [
    'path'            => __DIR__.'/..',
    'path.cache'      => __DIR__.'/cache',
    'path.logs'       => __DIR__.'/logs',
    'path.temp'       => __DIR__.'/temp',
    'path.extensions' => __DIR__.'/../extensions',
    'path.storage'    => __DIR__.'/../storage',
    'path.themes'     => __DIR__.'/../themes',
    'path.vendor'     => __DIR__.'/../vendor',
    'config.file'     => __DIR__.'/../config.php'
]);

$config = new Config($values);
$config->load(__DIR__.'/config/app.php');

if ($values['config.file']) {
    $config->load($values['config.file']);
}

if ($config['app.nocache']) {
    $config->set('cache.storage', 'array');
}

$values['config'] = $config;

return $values;