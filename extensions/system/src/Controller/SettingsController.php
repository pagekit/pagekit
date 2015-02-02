<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Config\Config;

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
        $supported = App::module('system/cache')->supports();

        $caches = [
            'auto'   => ['name' => '', 'supported' => true],
            'apc'    => ['name' => 'APC', 'supported' => in_array('apc', $supported)],
            'xcache' => ['name' => 'XCache', 'supported' => in_array('xcache', $supported)],
            'file'   => ['name' => 'File', 'supported' => in_array('file', $supported)]
        ];

        $caches['auto']['name'] = 'Auto ('.$caches[end($supported)]['name'].')';

        $countries = App::countries();
        $languages = App::languages();
        $timezones = $this->getTimezones();
        $codes     = ['en_US'];
        $locales   = [];

        foreach (App::finder()->directories()->depth(0)->in('extensions/system/languages')->name('/^[a-z]{2}(_[A-Z]{2})?$/') as $dir) {
            $code = $codes[] = $dir->getFileName();

            list($lang, $country) = explode('_', $code);

            $locales[$code] = $languages->isoToName($lang).' - '.$countries->isoToName($country);
        }

        $ssl    = extension_loaded('openssl');
        $sqlite = class_exists('SQLite3') || (class_exists('PDO') && in_array('sqlite', \PDO::getAvailableDrivers(), true));

        return ['head.title' => __('Settings'), 'option' => App::option(), 'config' => $this->config, 'cache' => $this->config->get('cache.cache.storage', 'auto'), 'caches' => $caches, 'locales' => $locales, 'timezones' => $timezones, 'tab' => $tab, 'ssl' => $ssl, 'sqlite' => $sqlite];
    }

    /**
     * @Request({"config": "array", "option": "array", "tab": "int"}, csrf=true)
     */
    public function saveAction($data, $option, $tab = 0)
    {
        // TODO: validate
        $data['app.debug'] = @$data['app.debug'] ?: '0';
        $data['profiler.enabled'] = @$data['profiler.enabled'] ?: '0';
        $data['app.nocache'] = @$data['app.nocache'] ?: '0';
        $data['cache.cache.storage'] = @$data['cache.cache.storage'] ?: 'auto';
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

    /**
     * @Request({"option": "array"}, csrf=true)
     * @Response("json")
     */
    public function testSmtpConnectionAction($option = [])
    {
        try {

            $option = array_merge([
                'system:mail.port' => '',
                'system:mail.host' => '',
                'system:mail.username' => '',
                'system:mail.password' => '',
                'system:mail.encryption' => ''
            ], $option);

            App::mailer()->testSmtpConnection($option['system:mail.host'], $option['system:mail.port'], $option['system:mail.username'], $option['system:mail.password'], $option['system:mail.encryption']);

            return ['success' => true, 'message' => __('Connection established!')];

        } catch (\Exception $e) {

            return ['success' => false, 'message' => sprintf(__('Connection not established! (%s)'), $e->getMessage())];
        }
    }

    /**
     * Note: If the mailer is accessed prior to this controller action, this will possibly test the wrong mailer
     *
     * @Request({"option": "array"}, csrf=true)
     * @Response("json")
     */
    public function testSendEmailAction($option = [])
    {
        try {

            $option = array_merge([
                'system:mail.driver' => '',
                'system:mail.port' => '',
                'system:mail.host' => '',
                'system:mail.username' => '',
                'system:mail.password' => '',
                'system:mail.encryption' => '',
                'system:mail.from.name' => '',
                'system:mail.from.address' => ''
            ], $option);

            foreach ($option as $key => $value) {
                App::option()->set($key, $value);
            }

            $response['success'] = (bool) App::mailer()->create(__('Test email!'), __('Testemail'), $option['system:mail.from.address'])->send();
            $response['message'] = $response['success'] ? __('Mail successfully sent!') : __('Mail delivery failed!');

        } catch (\Exception $e) {

            $response = ['success' => false, 'message' => sprintf(__('Mail delivery failed! (%s)'), $e->getMessage())];
        }

        return $response;
    }

    /**
     * @return array
     */
    protected function getTimezones()
    {
        $timezones = [];

        foreach (\DateTimeZone::listIdentifiers() as $timezone) {

            $parts = explode('/', $timezone);

            if (count($parts) > 2) {
                $region = $parts[0];
                $name = $parts[1].' - '.$parts[2];
            } elseif (count($parts) > 1) {
                $region = $parts[0];
                $name = $parts[1];
            } else {
                $region = 'Other';
                $name = $parts[0];
            }

            $timezones[$region][$timezone] = str_replace('_', ' ', $name);
        }

        return $timezones;
    }
}
