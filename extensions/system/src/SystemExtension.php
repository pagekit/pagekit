<?php

namespace Pagekit\System;

use Pagekit\Application as App;
use Pagekit\System\Event\CanonicalListener;
use Pagekit\System\Event\FrontpageListener;
use Pagekit\System\Event\MaintenanceListener;
use Pagekit\System\Event\MigrationListener;
use Pagekit\System\Event\SystemListener;
use Pagekit\System\Event\ThemeListener;
use Pagekit\System\Event\WidgetListener as ThemeWidgetListener;
use Pagekit\System\Helper\SystemInfoHelper;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;

class SystemExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        if (!$app['module']['framework']->config('debug')) {
            $app->subscribe(new ExceptionListener('Pagekit\System\Exception\ExceptionController::showAction'));
        }

        $app->subscribe(
            new CanonicalListener,
            new FrontpageListener,
            new MaintenanceListener,
            new MigrationListener,
            new SystemListener,
            new ThemeListener,
            new ThemeWidgetListener
        );

        $app['module']['framework/auth']->config['rememberme.key'] = $this->config('key');

        $app['path.storage'] = rtrim($app['path'].'/'.ltrim(($this->config['storage'] ?: 'storage'), '/'), '/');

        $app['db.em']; // -TODO- fix me

        $app['system'] = $this;

        $app['systemInfo'] = function() {
            return new SystemInfoHelper;
        };
    }

    /**
     * @{inheritdoc}
     */
    public function enable()
    {
        if ($version = App::migrator()->create('extensions/system/migrations', App::option('system:version'))->run()) {
            App::option()->set('system:version', $version);
        }

        foreach (['blog', 'page'] as $extension) {
            if ($extension = App::module($extension)) {
                $extension->enable();
            }
        }
    }

    /**
     * Clear cache on kernel terminate event.
     */
    public function clearCache(array $options = [])
    {
        App::on('kernel.terminate', function() use ($options) {
            $this->doClearCache($options);
        }, -512);
    }

    /**
     * TODO: clear opcache
     */
    public function doClearCache(array $options = [])
    {
        // clear cache
        if (empty($options) || isset($options['cache'])) {
            App::cache()->flushAll();

            foreach (glob(App::get('path.cache') . '/*.cache') as $file) {
                @unlink($file);
            }
        }

        // clear compiled template files
        if (empty($options) || isset($options['templates'])) {
            App::file()->delete(App::get('path.cache').'/templates');
        }

        // clear temp folder
        if (isset($options['temp'])) {
            foreach (App::finder()->in(App::get('path.temp'))->depth(0)->ignoreDotFiles(true) as $file) {
                App::file()->delete($file->getPathname());
            }
        }
    }
}
