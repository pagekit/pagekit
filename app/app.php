<?php

use Pagekit\Application as App;

$loader = require __DIR__.'/autoload.php';
$config = require __DIR__.'/config.php';

$app = new App($config);
$app['autoloader'] = $loader;
$app['autoloader']->addPsr4('Pagekit\\System\\', $app['path.extensions'].'/system/src');

date_default_timezone_set($app['config']['app.timezone']);

foreach ($app['config']['app.providers'] as $provider) {
    $app->register($provider);
}

$app['module']->addPath($app['path.extensions'].'/*/extension.php');
$app['module']->addPath($app['path.themes'].'/*/theme.php');

try {

    class InstallerException extends RuntimeException {}

    if (!$app['config.file']) {
        throw new InstallerException('No config.');
    }

    $app['db']->connect();

    if (!$app['cache']->fetch('installed')) {

        if (!$app['db']->getSchemaManager()->tablesExist($app['db']->getPrefix().'system_option')) {
            throw new InstallerException('Not installed.');
        }

        $app['cache']->save('installed', true);
    }

    $app['modules'] = array_merge($app['option']->get('system:extensions', []), ['system']);

} catch (InstallerException $e) {

    $requirements = require __DIR__.'/requirements.php';

    if ($failed = $requirements->getFailedRequirements()) {
        require $app['path.extensions'].'/installer/views/requirements.php';
        exit;
    }

    $app['config']->load(__DIR__.'/config/install.php');
    $app['modules'] = ['installer'];

}

return $app;
