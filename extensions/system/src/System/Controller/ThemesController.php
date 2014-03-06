<?php

namespace Pagekit\System\Controller;

use Pagekit\Component\File\Archive\Zip;
use Pagekit\Component\Package\Loader\JsonLoader;
use Pagekit\Component\Package\Repository\RemoteRepository;
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
    protected $remote;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->themes = $this('themes');
        $this->temp   = $this('path.temp');
        $this->api    = $this('config')->get('api.url');
        $this->apiKey = $this('option')->get('system:api.key');
        $this->remote = new RemoteRepository($this->api.'/package');
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

            if ($this('option')->get('system:theme', $this('config')->get('theme.default')) == $name) {
                $current = $package;
            }

            $packages[$name] = $package;
            $packagesJson[$name] = $package->getVersion();
        }

        return array('meta.title' => __('Themes'), 'api' => $this->api, 'key' => $this->apiKey, 'current' => $current, 'packages' => $packages, 'packagesJson' => json_encode($packagesJson));
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

            $this('option')->set('system:theme', $theme->getName(), true);

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
     * @View("system/admin/settings/themes.install.razr.php", layout=false)
     */
    public function uploadAction()
    {
        try {

            $file = $this('request')->files->get('file');

            if ($file === null || !$file->isValid()) {
                throw new Exception(__('No file uploaded.'));
            }

            $package = $this->load($upload = $file->getPathname());

            Zip::extract($upload, "{$this->temp}/".($path = sha1($upload)));

            $status = null;
            $name   = $package->getName();

            if ($installed = $this->themes->getRepository()->findPackage($name) and $installed->compare($package, '>')) {
                $status = 'old';
            } elseif ($update = $this->remote->findPackage($name) and $package->compare($update, '<')) {
                $status = 'update';
            }

            if ($checksum = $this->remote->findPackage($name, $package->getVersion())) {
                $checksum = $checksum->getDistSha1Checksum() == sha1_file("{$this->temp}/{$path}");
            }

            return array('meta.title' => __('Install Theme'), 'path' => $path, 'package' => $package, 'status' => $status, 'checksum' => $checksum);

        } catch (Exception $e) {
            return $e->getMessage();
        }
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

    protected function load($file)
    {
        try {

            if (is_dir($file)) {

                $json = $file . '/theme.json';

            } elseif (is_file($file)) {

                $zip = new \ZipArchive;

                if ($zip->open($file) === true) {
                    $json = $zip->getFromName('theme.json');
                    $zip->close();
                }
            }

            if (isset($json) && $json) {

                $loader = new JsonLoader(new ThemeLoader);
                $package = $loader->load($json);

                return $package;
            }

            throw new Exception;

        } catch (\Exception $e) {
            throw new Exception(__('Can\'t load theme.json from package.'));
        }
    }
}
