<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Config\Config;
use Pagekit\System\Event\SettingsEvent;


/**
 * @Access("system: access settings", admin=true)
 */
class SettingsController extends Controller
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var string
     */
    protected $configFile;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->config = new Config;
        $this->config->load($this->configFile = App::get('config.file'));
    }

    /**
     * @Request({"tab": "int"})
     * @Response("extensions/system/views/admin/settings/settings.razr")
     */
    public function indexAction($tab = 0)
    {
        $ssl    = extension_loaded('openssl');
        $sqlite = class_exists('SQLite3') || (class_exists('PDO') && in_array('sqlite', \PDO::getAvailableDrivers(), true));

        return ['head.title' => __('Settings'), 'option' => App::option(), 'config' => $this->config, 'tab' => $tab, 'ssl' => $ssl, 'sqlite' => $sqlite, 'additionals' => App::trigger('system.settings.edit', new SettingsEvent)->get()];
    }

    /**
     * @Request({"config": "array", "option": "array", "tab": "int"}, csrf=true)
     */
    public function saveAction($data, $option, $tab = 0)
    {
        // TODO: validate
        $data['app.debug'] = @$data['app.debug'] ?: '0';

        // TODO remove
        $data['profiler.enabled'] = @$data['profiler.enabled'] ?: '0';
        $option['system:app.site_title'] = @$option['system:app.site_title'] ?: '';
        $option['system:maintenance.enabled'] = @$option['system:maintenance.enabled'] ?: '0';

        foreach ($data as $key => $value) {
            $this->config->set($key, $value);
        }

        file_put_contents($this->configFile, $this->config->dump());

        foreach ($option as $key => $value) {
            App::option()->set($key, $value, true);
        }

        if ($data['cache.cache.storage'] != App::config('cache.cache.storage') || $data['app.debug'] != App::config('app.debug')) {
            App::module('system')->clearCache();
        }

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($this->configFile);
        }

        App::message()->success(__('Settings saved.'));

        return $this->redirect('@system/settings', compact('tab'));
    }
}
