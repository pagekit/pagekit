<?php

namespace Pagekit\System\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;

/**
 * @Access("system: manage extensions", admin=true)
 */
class ExtensionsController extends Controller
{
    protected $extensions;
    protected $temp;
    protected $api;
    protected $apiKey;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->extensions = $this('extensions');
        $this->temp       = $this('path.temp');
        $this->api        = $this('config')->get('api.url');
        $this->apiKey     = $this('option')->get('system:api.key');
    }

    /**
     * @View("system/admin/settings/extensions.razr.php")
     */
    public function indexAction()
    {
        $packages = array();
        $packagesJson = array();

        foreach ($this->extensions->getRepository()->getPackages() as $package) {
            if (!$this->isCore($name = $package->getName())) {
                $packages[$name] = $package;
                $packagesJson[$name] = $package->getVersion();
            }
        }

        return array('head.title' => __('Extensions'), 'api' => $this->api, 'key' => $this->apiKey, 'packages' => $packages, 'packagesJson' => json_encode($packagesJson));
    }

    /**
     * @Request({"name"})
     */
    public function enableAction($name)
    {
        try {

            if ($this->isCore($name)) {
                throw new Exception(__('Core extensions may not be enabled.'));
            }

            if (!$this->extensions->get($name)) {
                $this->extensions->load($name);
            }

            if (!$extension = $this->extensions->get($name)) {
                throw new Exception(__('Unable to enable extension "%name%".', array('%name%' => $name)));
            }

            $this->enable($extension);

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect('@system/extensions/index');
    }

    /**
     * @Request({"name"})
     */
    public function disableAction($name)
    {
        try {

            if ($this->isCore($name)) {
                throw new Exception(__('Core extensions may not be disabled.'));
            }

            if (!$extension = $this->extensions->get($name)) {
                throw new Exception(__('Extension "%name%" has not been loaded.', array('%name%' => $name)));
            }

            $this->disable($extension);

            $this('system')->clearCache();

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect('@system/extensions/index');
    }

    /**
     * @Request({"name"})
     */
    public function uninstallAction($name)
    {
        try {

            if ($this->isCore($name)) {
                throw new Exception(__('Core extensions may not be uninstalled.'));
            }

            if (!$this->extensions->get($name)) {
                $this->extensions->load($name);
            }

            if (!$extension = $this->extensions->get($name)) {
                throw new Exception(__('Unable to uninstall extension "%name%".', array('%name%' => $name)));
            }

            $this->disable($extension);

            $extension->uninstall();

            $this->extensions->getInstaller()->uninstall($this->extensions->getRepository()->findPackage($name));

            $this('system')->clearCache();

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect('@system/extensions/index');
    }

    /**
     * @Request({"name"})
     */
    public function settingsAction($name)
    {
        try {

            if (!$extension = $this->extensions->get($name) or !$url = $extension->getConfig('settings')) {
                throw new Exception(__('Invalid extension.'));
            }

            return $this('router')->call($url);

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect('@system/system/index');
    }

    protected function enable($extension)
    {
        $this('option')->set('system:extensions', array_unique(array_merge($this('option')->get('system:extensions', array()), array($extension->getName()))), true);

        $extension->enable();
    }

    protected function disable($extension)
    {
        $this('option')->set('system:extensions', array_values(array_diff($this('option')->get('system:extensions', array()), array($extension->getName()))), true);

        $extension->disable();
    }

    protected function isCore($name)
    {
        return in_array($name, $this('config')->get('extension.core', array()));
    }
}
