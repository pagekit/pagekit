<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Application\Exception;
use Pagekit\System\Event\ThemeEvent;

/**
 * @Access("system: manage themes", admin=true)
 */
class ThemesController extends Controller
{
    /**
     * @Response("extensions/system/views/admin/themes/index.razr")
     */
    public function indexAction()
    {
        $current = null;
        $packages = [];
        $packagesJson = [];

        foreach (App::package()->getRepository('theme')->getPackages() as $package) {

            $name = $package->getName();

            if (App::system()->config('theme.site') == $name) {
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
                'table' => App::tmpl('extensions/system/views/admin/themes/table.razr', ['packages' => $packages, 'current' => $current])
            ]);
        }

        return ['head.title' => __('Themes'), 'api' => App::system()->config('api.url'), 'key' => App::option('system:api.key'), 'current' => $current, 'packages' => $packages, 'packagesJson' => json_encode($packagesJson)];
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

            App::module()->load($name);

            if (!$theme = App::module($name)) {
                throw new Exception(__('Unable to enable theme "%name%".', ['%name%' => $name]));
            }

            $settings = App::option('system:settings', []);
            $settings['theme.site'] = $theme->name;
            App::option()->set('system:settings', $settings, true);

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

            if (!$theme = App::package()->getRepository('theme')->findPackage($name)) {
                throw new Exception(__('Unable to uninstall theme "%name%".', ['%name%' => $name]));
            }

            App::package()->getInstaller('theme')->uninstall($theme);
            App::system()->clearCache();

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

            if (!$theme = App::module($name) or !$tmpl = $theme->config('settings.view')) {
                throw new Exception(__('Invalid theme.'));
            }

            $event = App::trigger('system.theme.edit', new ThemeEvent($theme, $theme->config));

            return App::tmpl($tmpl, ['theme' => $theme, 'params' => $event->getParams()]);

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

            if (!$theme = App::module()->get($name)) {
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
