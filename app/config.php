<?php

$values = array_map('realpath', [
    'path'            => __DIR__.'/..',
    'path.temp'       => __DIR__.'/../tmp',
    'path.cache'      => __DIR__.'/../tmp/cache',
    'path.logs'       => __DIR__.'/../tmp/logs',
    'path.extensions' => __DIR__.'/../extensions',
    'path.storage'    => __DIR__.'/../storage',
    'path.themes'     => __DIR__.'/../themes',
    'path.vendor'     => __DIR__.'/../vendor',
    'config.file'     => __DIR__.'/../config.php'
]);

extract($values);

return array_replace(compact('values'), require __DIR__.'/system/config.php');
