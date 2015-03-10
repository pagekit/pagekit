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
     * @Response("extensions/system/views/admin/settings/settings.php")
     */
    public function indexAction()
    {
        App::view()->meta(['title' => __('Settings')]);
        App::view()->script('settings', 'extensions/system/app/settings.js', 'vue-system');
        App::view()->data('settings', [
            'config' => $this->config->getValues(),
        ]);

        return ['additionals' => App::trigger('system.settings.edit', new SettingsEvent($this->config))->get()];
    }

    /**
     * @Request({"config": "array", "option": "array"}, csrf=true)
     * @Response("json")
     */
    public function saveAction($config, $option)
    {
        $option = new \ArrayObject($option);

        foreach ($config as $module => $value) {
            $this->config->set($module, $value);
        }

        App::trigger('system.settings.save', [$this->config, $option]);

        file_put_contents($this->configFile, $this->config->dump());

        foreach ($option as $module => $value) {

            if (!preg_match('/:settings$/i', $module)) {
                $module .= ':settings';
            }

            App::option()->set($module, $value, true);
        }

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($this->configFile);
        }

        return ['success'];
    }
}
