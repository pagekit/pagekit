<?php

namespace Pagekit\System;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\System\Event\CanonicalListener;
use Pagekit\System\Event\FrontpageListener;
use Pagekit\System\Event\MaintenanceListener;
use Pagekit\System\Event\MigrationListener;
use Pagekit\System\Event\SystemListener;
use Pagekit\System\Event\ThemeListener;
use Pagekit\System\Event\WidgetListener as ThemeWidgetListener;
use Pagekit\System\Helper\SystemInfoHelper;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;

class SystemModule extends Module
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

        $this->config['storage'] = '/'.trim(($this->config['storage'] ?: 'storage'), '/');
        $app['path.storage'] = rtrim($app['path'].$this->config['storage'], '/');

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
        if ($version = App::migrator()->create('app/modules/system/migrations', App::option('system:version'))->run()) {
            App::option()->set('system:version', $version);
        }

        foreach (['blog', 'page'] as $extension) {
            if ($extension = App::module($extension)) {
                $extension->enable();
            }
        }
    }
}
