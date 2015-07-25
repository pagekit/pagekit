<?php

namespace Pagekit\System\Controller;

use GuzzleHttp\Client;
use Pagekit\Application as App;
use Pagekit\Filesystem\Archive\Zip;
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
        $client->setDefaultOption('query/api_key', App::module('system/package')->config('api.key'));

        $this->installer = new PackageInstaller($client);
    }

    public function themesAction()
    {
        $packages = App::package()->all('pagekit-theme');

        foreach ($packages as $package) {
            if ($module = App::module($package->getName())) {

                if ($settings = $module->get('settings') and $settings[0] === '@') {
                    $settings = App::url($settings);
                }

                $package->set('settings', $settings);
                $package->set('config', $module->config);

            }

            $package->set('enabled', (bool) $module);
        }

        return [
            '$view' => [
                'title' => __('Themes'),
                'name'  => 'system:modules/package/views/themes.php'
            ],
            '$data' => [
                'api' => App::module('system/package')->config('api'),
                'packages' => $packages
            ]
        ];
    }

    public function extensionsAction()
    {
        $packages = App::package()->all('pagekit-extension');

        foreach ($packages as $package) {
            if ($module = App::module($package->getName())) {

                if ($settings = $module->get('settings') and $settings[0] === '@') {
                    $settings = App::url($settings);
                }

                $package->set('enabled', true);
                $package->set('settings', $settings);
                $package->set('config', $module->config);
                $package->set('permissions', (bool) $module->get('permissions'));
            }
        }

        return [
            '$view' => [
                'title' => __('Extensions'),
                'name'  => 'system:modules/package/views/extensions.php'
            ],
            '$data' => [
                'api' => App::module('system/package')->config('api'),
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

        App::trigger('enable', [$module]);
        App::trigger("enable.$name", [$module]);

        if ($package->getType() == 'theme') {
            App::config('system')->set('site.theme', $name);
        } elseif ($package->getType() == 'pagekit-extension') {
            App::config('system')->push('extensions', $name);
        }

        App::exception()->setHandler($handler);

        return ['message' => 'success'];
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

        App::trigger('disable', [$module]);
        App::trigger("disable.$name", [$module]);

        if ($package->getType() == 'extension') {
            App::config('system')->pull('extensions', $name);
        }

        App::module('system/cache')->clearCache();

        return ['message' => 'success'];
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

        $extra = $package->get('extra');

        if (isset($extra['image'])) {
            $extra['image'] = App::url()->getStatic("{$temp}/$path/".$extra['image']);
        } else {
            $extra['image'] = App::url('app/system/assets/images/placeholder-icon.svg');
        }

        $package->set('extra', $extra);
        $package->set('shasum', sha1_file($upload));

        return [
            'package' => $package,
            'install' => $path
        ];
    }

    /**
     * @Request({"package": "array", "path": "alnum"}, csrf=true)
     */
    public function installAction($package = null, $path = '')
    {
        $temp = App::get('path.temp');

        try {

            if ($package = App::package()->load($package)) {
                $path = sha1(json_encode($package));
                $this->installer->download($package, "{$temp}/{$path}");
            }

            if (!$path) {
                throw new \Exception(__('Path not found.'));
            }

            $package = $this->loadPackage($path = "{$temp}/{$path}");
            $name    = $package->getName();

            if ($enabled = (bool) App::module($name)) {
                $this->disableAction($name);
            }

            if ($package->get('type') == 'extension') {
                $this->installer->install($package, App::get('path.extensions'));
            }

            if ($package->get('type') == 'theme') {
                $this->installer->install($package, App::get('path.themes'));
            }

            App::module('system/cache')->clearCache();

            if ($enabled) {
                $this->enableAction($name);
            }

        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (strpos($path, $temp) === 0 && file_exists($path)) {
            App::file()->delete($path);
        }

        if (isset($error)) {
            App::abort(400, $error);
        }

        return ['message' => 'success', 'package' => App::package($name)];
    }

    /**
     * @Request({"name"}, csrf=true)
     */
    public function uninstallAction($name)
    {
        if (!$package = App::package($name)) {
            App::abort(400, __('Unable to find "%name%".', ['%name%' => $name]));
        }

        if (!App::module($name)) {
            App::module()->load($name);
        }

        if (!$module = App::module($name)) {
            App::abort(400, __('Unable to uninstall "%name%".', ['%name%' => $name]));
        }

        $this->disableAction($name);

        App::trigger('uninstall', [$module]);
        App::trigger("uninstall.$name", [$module]);

        $this->installer->uninstall($package);

        App::module('system/cache')->clearCache();

        return ['message' => 'success'];
    }

    protected function loadPackage($file)
    {
        try {

            if (is_dir($file)) {

                $json = realpath("$file/theme.json") ?: realpath("$file/extension.json");

            } elseif (is_file($file)) {

                $zip = new \ZipArchive;

                if ($zip->open($file) === true) {
                    $json = $zip->getFromName('theme.json') ?: $zip->getFromName('extension.json');
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

    /**
     * @param  string $name
     * @return callable|null
     */
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

            App::response()->json($message, 500)->send();
        });
    }
}
