<?php

namespace Pagekit\Theme\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
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
        $this->themes = $this['themes'];
        $this->api    = $this['config']->get('api.url');
        $this->apiKey = $this['option']->get('system:api.key');
    }

    /**
     * @Response("extension://system/views/admin/themes/index.razr")
     */
    public function indexAction()
    {
        $current = null;
        $packages = [];
        $packagesJson = [];

        foreach ($this->themes->getRepository()->getPackages() as $package) {

            $name = $package->getName();

            if ($this['config']->get('theme.site') == $name) {
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

        if ($this['request']->isXmlHttpRequest()) {
            return $this['response']->json([
                'table' => $this['view']->render('extension://system/views/admin/themes/table.razr', ['packages' => $packages, 'current' => $current])
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

            $handler = $this['exception']->setHandler(function($exception) {

                while (ob_get_level()) {
                    ob_get_clean();
                }

                $this['response']->json(['error' => true, 'message' => __('Unable to activate theme.<br>The theme triggered a fatal error.')])->send();
            });

            $this->themes->load($name);

            if (!$theme = $this->themes->get($name)) {
                throw new Exception(__('Unable to enable theme "%name%".', ['%name%' => $name]));
            }

            $theme->boot($this->getApplication());

            $this['option']->set('system:theme.site', $theme->getName(), true);
            $this['exception']->setHandler($handler);

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
            $this['system']->clearCache();

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

            $event = $this['events']->dispatch('system.theme.edit', new ThemeEvent($theme, $theme->getParams()));

            return $this['view']->render($tmpl, ['theme' => $theme, 'params' => $event->getParams()]);

        } catch (Exception $e) {

            $this['message']->error($e->getMessage());
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

            $event = $this['events']->dispatch('system.theme.save', new ThemeEvent($theme, $params));

            $this['option']->set("$name:settings", $event->getParams(), true);
            $this['message']->success(__('Settings saved.'));

            return $this->redirect('@system/themes/settings', compact('name'));

        } catch (Exception $e) {

            $this['message']->error($e->getMessage());
        }

        return $this->redirect('@system/system');
    }
}
