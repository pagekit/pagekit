<?php

namespace Pagekit\Theme\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Application\Exception;
use Pagekit\Theme\Event\ThemeEvent;

/**
 * @Access("system: manage themes", admin=true)
 */
class ThemesController extends Controller
{
    protected $themes;
    protected $api;
    protected $apiKey;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->themes = App::theme();
        $this->api    = App::config('api.url');
        $this->apiKey = App::option('system:api.key');
    }

    /**
     * @Response("extensions/system/views/admin/themes/index.razr")
     */
    public function indexAction()
    {
        $current = null;
        $packages = [];
        $packagesJson = [];

        foreach ($this->themes->getRepository()->getPackages() as $package) {

            $name = $package->getName();

            if (App::config('theme.site') == $name) {
                $current = $package;
            }

            $packages[$name] = $package;
            $packagesJson[$name] = $package->getVersion();
        }

        uasort($packages, function($themeA, $themeB) use ($current) {
            if ($current === $themeA) {
                return -1;
            } elseif ($current === $themeB) {
                return 1;
            }

            return strcmp($themeA->getName(), $themeB->getName());
        });

        if (App::request()->isXmlHttpRequest()) {
            return App::response()->json([
                'table' => App::view('extensions/system/views/admin/themes/table.razr', ['packages' => $packages, 'current' => $current])
            ]);
        }

        return ['head.title' => __('Themes'), 'api' => $this->api, 'key' => $this->apiKey, 'current' => $current, 'packages' => $packages, 'packagesJson' => json_encode($packagesJson)];
    }

    /**
     * @Request({"name"}, csrf=true)
     * @Response("json")
     */
    public function enableAction($name)
    {
        try {

            ini_set('display_errors', 0);

            $handler = App::exception()->setHandler(function($exception) {

                while (ob_get_level()) {
                    ob_get_clean();
                }

                App::response()->json(['error' => true, 'message' => __('Unable to activate theme.<br>The theme triggered a fatal error.')])->send();
            });

            $this->themes->load($name);

            if (!$theme = $this->themes->get($name)) {
                throw new Exception(__('Unable to enable theme "%name%".', ['%name%' => $name]));
            }

            $theme->boot(App::getInstance());

            App::option()->set('system:theme.site', $theme->getName(), true);
            App::exception()->setHandler($handler);

            return ['message' => __('Theme enabled.')];

        } catch (Exception $e) {

            return ['message' => $e->getMessage(), 'error' => true];
        }
    }

    /**
     * @Request({"name"}, csrf=true)
     * @Response("json")
     */
    public function uninstallAction($name)
    {
        try {

            if (!$theme = $this->themes->getRepository()->findPackage($name)) {
                throw new Exception(__('Unable to uninstall theme "%name%".', ['%name%' => $name]));
            }

            $this->themes->getInstaller()->uninstall($theme);
            App::extension('system')->clearCache();

            return ['message' => __('Theme uninstalled.')];

        } catch (Exception $e) {

            return ['message' => $e->getMessage(), 'error' => true];
        }
    }

    /**
     * @Request({"name"})
     */
    public function settingsAction($name)
    {
        try {

            if (!$theme = $this->themes->get($name) or !$tmpl = $theme->getConfig('parameters.settings.view')) {
                throw new Exception(__('Invalid theme.'));
            }

            $event = App::trigger('system.theme.edit', new ThemeEvent($theme, $theme->getParams()));

            return App::view($tmpl, ['theme' => $theme, 'params' => $event->getParams()]);

        } catch (Exception $e) {

            App::message()->error($e->getMessage());
        }

        return $this->redirect('@system/system');
    }

    /**
     * @Request({"name", "params": "array"})
     */
    public function saveSettingsAction($name, $params = [])
    {
        try {

            if (!$theme = $this->themes->get($name)) {
                throw new Exception(__('Invalid theme.'));
            }

            $event = App::trigger('system.theme.save', new ThemeEvent($theme, $params));

            App::option()->set("$name:settings", $event->getParams(), true);
            App::message()->success(__('Settings saved.'));

            return $this->redirect('@system/themes/settings', compact('name'));

        } catch (Exception $e) {

            App::message()->error($e->getMessage());
        }

        return $this->redirect('@system/system');
    }
}
