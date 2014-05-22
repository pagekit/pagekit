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
    protected $temp;
    protected $api;
    protected $apiKey;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->themes = $this('themes');
        $this->temp   = $this('path.temp');
        $this->api    = $this('config')->get('api.url');
        $this->apiKey = $this('option')->get('system:api.key');
    }

    /**
     * @View("system/admin/settings/themes.razr.php")
     */
    public function indexAction()
    {
        $current = null;
        $packages = array();
        $packagesJson = array();

        foreach ($this->themes->getRepository()->getPackages() as $package) {

            $name = $package->getName();

            if ($this('config')->get('theme.site') == $name) {
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

        return array('head.title' => __('Themes'), 'api' => $this->api, 'key' => $this->apiKey, 'current' => $current, 'packages' => $packages, 'packagesJson' => json_encode($packagesJson));
    }

    /**
     * @Request({"name"})
     * @Token
     */
    public function enableAction($name)
    {
        try {

            $this->themes->load($name);

            if (!$theme = $this->themes->get($name)) {
                throw new Exception(__('Unable to enable theme "%name%".', array('%name%' => $name)));
            }

            $this('option')->set('system:theme.site', $theme->getName(), true);

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect('@system/themes/index');
    }

    /**
     * @Request({"name"})
     * @Token
     */
    public function uninstallAction($name)
    {
        try {

            if (!$theme = $this->themes->getRepository()->findPackage($name)) {
                throw new Exception(__('Unable to uninstall theme "%name%".', array('%name%' => $name)));
            }

            $this->themes->getInstaller()->uninstall($theme);

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        $this('system')->clearCache();

        return $this->redirect('@system/themes/index');
    }

    /**
     * @Request({"name"})
     */
    public function settingsAction($name)
    {
        try {

            if (!$theme = $this->themes->get($name) or !$tmpl = $theme->getConfig('settings.system')) {
                throw new Exception(__('Invalid theme.'));
            }

            $config = $this('option')->get("$name:config", array());
            $event  = $this('events')->dispatch('system.theme.edit', new ThemeEvent($theme, $config));

            return $this('view')->render($tmpl, array('theme' => $theme, 'config' => $event->getConfig()));

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect('@system/system/index');
    }

    /**
     * @Request({"name", "config": "array"})
     */
    public function saveSettingsAction($name, $config = array())
    {
        try {

            if (!$theme = $this->themes->get($name)) {
                throw new Exception(__('Invalid theme.'));
            }

            $event = $this('events')->dispatch('system.theme.save', new ThemeEvent($theme, $config));

            $this('option')->set("$name:config", $event->getConfig(), true);
            $this('message')->success(__('Settings saved.'));

            return $this->redirect('@system/themes/settings', compact('name'));

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect('@system/system/index');
    }
}
