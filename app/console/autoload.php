<?php

$loader = require __DIR__ . '/../../autoload.php';

$app = new App($this->config['values']);
$app['autoloader'] = $loader;

$app['module']->addPath([
__DIR__ . '/../../modules/*/index.php',
__DIR__ . '/../../system/index.php',
]);

$app['module']->addLoader(new AutoLoader($loader));
$app['module']->addLoader(new ConfigLoader($this->config));
$app['module']->addLoader(new ConfigLoader(require $app['config.file']));

$app['module']->addPath(__DIR__ . '/../console.php');
$app['module']->load('console');

return $app;