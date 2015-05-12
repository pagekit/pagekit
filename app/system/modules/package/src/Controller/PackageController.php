<?php

namespace Pagekit\System\Controller;

use GuzzleHttp\Client;
use Pagekit\Application as App;
use Pagekit\Filesystem\Archive\Zip;
use Pagekit\System\Extension;
use Pagekit\System\Package\PackageDownloader;
use Pagekit\System\Package\PackageInstaller;

/**
 * @Access("system: manage packages", admin=true)
 */
class PackageController
{
    protected $installer;

    public function __construct()
    {
        $client = new Client;
        $client->setDefaultOption('query/api_key', App::system()->config('api.key'));

        $this->installer = new PackageInstaller($client);
    }

    public function themesAction()
    {
        $packages = App::package()->all('theme');

        foreach ($packages as $package) {
            $package->set('enabled', App::module($package->getName()) != null);
        }

        return [
            '$view' => [
                'title' => __('Themes'),
                'name'  => 'system:modules/package/views/themes.php'
            ],
            '$data' => [
                'api' => App::system()->config('api'),
                'packages' => $packages
            ]
        ];
    }

    public function extensionsAction()
    {
        $packages = App::package()->all('extension');

        foreach ($packages as $package) {
            $package->set('enabled', App::module($package->getName()) != null);
        }

        return [
            '$view' => [
                'title' => __('Extensions'),
                'name'  => 'system:modules/package/views/extensions.php'
            ],
            '$data' => [
                'api' => App::system()->config('api'),
                'packages' => $packages
            ]
        ];
    }

    /**
     * @Request({"name"}, csrf=true)
     */
    public function enableAction($name)
    {
        $handler = $this->errorHandler($name);

        if (!$package = App::package($name)) {
            App::abort(400, __('Unable to find "%name%".', ['%name%' => $name]));
        }

        App::module()->load($name);

        if (!$module = App::module($name)) {
            App::abort(400, __('Unable to enable "%name%".', ['%name%' => $name]));
        }

        if ($package->getType() == 'theme') {

            App::config('system')->set('theme.site', $name);
            App::exception()->setHandler($handler);

            return ['message' => __('Theme "%name%" enabled.', ['%name%' => $name])];
        }

        if ($package->getType() == 'extension') {

            if ($module instanceof Extension) {
                $module->enable();
            }

            App::config('system')->push('extensions', $name);
            App::exception()->setHandler($handler);

            return ['message' => __('Extension "%name%" enabled.', ['%name%' => $name])];
        }
    }

    /**
     * @Request({"name"}, csrf=true)
     */
    public function disableAction($name)
    {
        if (!$package = App::package($name)) {
            App::abort(400, __('Unable to find "%name%".', ['%name%' => $name]));
        }

        if (!$module = App::module($name)) {
            App::abort(400, __('"%name%" has not been loaded.', ['%name%' => $name]));
        }

        if ($package->getType() == 'extension') {

            $module->disable();

            App::config('system')->pull('extensions', $name);
            App::module('system/cache')->clearCache();

            return ['message' => __('Extension "%name%" disabled.', ['%name%' => $name])];
        }
    }

    /**
     * @Request({"type"}, csrf=true)
     */
    public function uploadAction($type = null)
    {
        $temp = App::get('path.temp');
        $file = App::request()->files->get('file');

        if ($file === null || !$file->isValid()) {
            App::abort(400, __('No file uploaded.'));
        }

        $package = $this->loadPackage($upload = $file->getPathname());

        if ($type != $package->getType()) {
            App::abort(400, __('Invalid package type.'));
        }

        Zip::extract($upload, "{$temp}/".($path = sha1($upload)));

        $extra = $package->getExtra();

        if (isset($extra['image'])) {
            $extra['image'] = App::url("{$temp}/$path/".$extra['image']);
        } else {
            $extra['image'] = App::url('app/system/assets/images/placeholder-icon.svg');
        }

        return [
            'package' => [
                'name' => $package->getName(),
                'type' => $package->getType(),
                'title' => $package->getTitle(),
                'description' => $package->getDescription(),
                'version' => $package->getVersion(),
                'author' => $package->getAuthor(),
                'shasum' => sha1_file($upload),
                'extra' => $extra
            ],
            'install' => $path
        ];
    }

    /**
     * @Request({"package": "array", "path": "alnum"}, csrf=true)
     */
    public function installAction($package = null, $path = '')
    {
        try {

            $temp = App::get('path.temp');

            if ($package = App::package()->load($package)) {
                $path = sha1(json_encode($package));
                $this->installer->download($package, "{$temp}/{$path}");
            }

            if (!$path) {
                throw new \Exception(__('Path not found.'));
            }

            $package = $this->loadPackage($path = "{$temp}/{$path}");

            if ($package->getType() == 'extension') {
                $this->installer->install($package, App::get('path.extensions'));
            }

            if ($package->getType() == 'theme') {
                $this->installer->install($package, App::get('path.themes'));
            }

            App::module('system/cache')->clearCache();

        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (strpos($path, $temp) === 0 && file_exists($path)) {
            App::file()->delete($path);
        }

        if (isset($error)) {
            App::abort(400, $error);
        }

        return ['message' => __('Package "%name%" installed.', ['%name%' => $package->getName()])];
    }

    /**
     * @Request({"name"}, csrf=true)
     */
    public function uninstallAction($name)
    {
        if (!$package = App::package($name)) {
            App::abort(400, __('Unable to find "%name%".', ['%name%' => $name]));
        }

        $type = $package->getType();

        if ($type == 'extension') {

            if (!App::module($name)) {
                App::module()->load($name);
            }

            if (!$module = App::module($name)) {
                App::abort(400, __('Unable to uninstall "%name%".', ['%name%' => $name]));
            }

            $module->disable();
            $module->uninstall();

            App::config('system')->pull('extensions', $name);
        }

        $this->installer->uninstall($package);

        App::module('system/cache')->clearCache();

        return ['message' => __('%name% uninstalled.', ['%name%' => $name])];
    }

    protected function loadPackage($file)
    {
        try {

            if (is_dir($file)) {

                $json = realpath("$file/theme.json") ?: realpath("$file/extension.json");

            } elseif (is_file($file)) {

                $zip = new \ZipArchive;

                if ($zip->open($file) === true) {
                    $json = $zip->getFromName("theme.json") ?: $zip->getFromName("extension.json");
                    $zip->close();
                }
            }

            if (isset($json) && $json) {
                return App::package()->load($json);
            }

            App::abort(400);

        } catch (\Exception $e) {

            App::abort(400, __('Can\'t load json file from package.'));
        }
    }

    protected function errorHandler($name)
    {
        ini_set('display_errors', 0);

        return App::exception()->setHandler(function ($exception) use ($name) {

            while (ob_get_level()) {
                ob_get_clean();
            }

            $message = __('Unable to activate "%name%".<br>A fatal error occured.', ['%name%' => $name]);

            if (App::debug()) {
                $message .= '<br><br>'.$exception->getMessage();
            }

            App::response()->json(['error' => true, 'message' => $message])->send();
        });
    }
}
