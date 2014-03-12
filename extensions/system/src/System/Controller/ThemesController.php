<?php

namespace Pagekit\System\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;

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

        return array('head.title' => __('Themes'), 'api' => $this->api, 'key' => $this->apiKey, 'current' => $current, 'packages' => $packages, 'packagesJson' => json_encode($packagesJson));
    }

    /**
     * @Request({"name"})
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

            if (!$theme = $this->themes->get($name) or !$url = $theme->getConfig('settings')) {
                throw new Exception(__('Invalid theme.'));
            }

            return $this('router')->call($url);

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect('@system/system/index');
    }
}
