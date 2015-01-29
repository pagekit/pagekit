<?php

namespace Pagekit;

use Pagekit\Application as App;
use Pagekit\Extension\Extension;
use Pagekit\Menu\Event\MenuListener;
use Pagekit\Menu\MenuProvider;
use Pagekit\System\Event\CanonicalListener;
use Pagekit\System\Event\FrontpageListener;
use Pagekit\System\Event\MaintenanceListener;
use Pagekit\System\Event\MigrationListener;
use Pagekit\System\Event\ResponseListener;
use Pagekit\System\Event\SystemListener;
use Pagekit\System\Helper\SystemInfoHelper;
use Pagekit\System\Mail\ImpersonatePlugin;
use Pagekit\Theme\Event\ThemeListener;
use Pagekit\Theme\Event\WidgetListener as ThemeWidgetListener;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;

class SystemExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(App $app, array $config)
    {
        if (!(isset($app['config']) ? $app['config']['app.debug'] : true)) {
            $app->subscribe(new ExceptionListener('Pagekit\System\Exception\ExceptionController::showAction'));
        }

        $app->subscribe(
            new CanonicalListener,
            new FrontpageListener,
            new MaintenanceListener,
            new MenuListener,
            new MigrationListener,
            new ResponseListener,
            new SystemListener,
            new ThemeListener,
            new ThemeWidgetListener
        );

        parent::load($app, $config);

        $this->mergeOptions();

        $app['db.em']; // -TODO- fix me

        $app['system'] = $this;

        $app['menus'] = function() {
            return new MenuProvider;
        };

        $app['systemInfo'] = function() {
            return new SystemInfoHelper;
        };

        $app->extend('mailer', function($mailer, $app) {

            $address = $app['config']->get('mail.from.address');
            $name    = $app['config']->get('mail.from.name');

            $mailer->registerPlugin(new ImpersonatePlugin($address, $name));

            return $mailer;
        });

        $app->factory('finder', function() {
            return Finder::create();
        });
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
            if ($extension = App::extension($extension)) {
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

    /**
     * Load controllers.
     *
     * @param array $config
     */
    public function loadControllers(array $config)
    {
        if (!isset($config['controllers'])) {
            return;
        }

        $name = $config['name'];

        foreach ((array) $config['controllers'] as $prefix => $controllers) {

            if (false === strpos($prefix, ':')) {
                $namespace = "@{$name}";
            } else {
                list($namespace, $prefix) = explode(':', $prefix);
            }

            App::controllers()->mount($prefix, $controllers, "$namespace/");
        }
    }

    /**
     * Load languages.
     *
     * @param string $path
     */
    public function loadLanguages($path)
    {
        $locale  = App::translator()->getLocale();
        $domains = [];

        foreach (glob($path.'/languages/'.$locale.'/*') ?: [] as $file) {

            $format = substr(strrchr($file, '.'), 1);
            $domain = basename($file, '.'.$format);

            if (in_array($domain, $domains)) {
                continue;
            }

            $domains[] = $domain;

            App::translator()->addResource($format, $file, $locale, $domain);
            App::translator()->addResource($format, $file, substr($locale, 0, 2), $domain);
        }
    }

    protected function mergeOptions()
    {
        $keys = [
            'app.frontpage',
            'app.locale',
            'app.locale_admin',
            'app.site_description',
            'app.site_title',
            'app.timezone',
            'mail.driver',
            'mail.encryption',
            'mail.from.address',
            'mail.from.name',
            'mail.host',
            'mail.password',
            'mail.port',
            'mail.username',
            'maintenance.enabled',
            'maintenance.msg',
            'theme.site'
        ];

        foreach ($keys as $key) {
            App::config()->set($key, App::option("system:$key", App::config($key)));
        }
    }
}
