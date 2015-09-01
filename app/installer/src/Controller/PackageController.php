<?php

namespace Pagekit\Installer\Controller;

use Pagekit\Application as App;
use Pagekit\Installer\Installer;

/**
 * @Access("system: manage packages", admin=true)
 */
class PackageController
{
    public function themesAction()
    {
        $packages = App::package()->all('pagekit-theme');

        foreach ($packages as $package) {
            if ($module = App::module($package->get('module'))) {

                if ($settings = $module->get('settings') and $settings[0] === '@') {
                    $settings = App::url($settings);
                }

                $package->set('enabled', true);
                $package->set('settings', $settings);
                $package->set('config', $module->config);
            }
        }

        return [
            '$view' => [
                'title' => __('Themes'),
                'name' => 'installer:views/themes.php'
            ],
            '$data' => [
                'api' => App::system()->config('api'),
                'packages' => $packages
            ]
        ];
    }

    public function extensionsAction()
    {
        $packages = App::package()->all('pagekit-extension');

        foreach ($packages as $package) {
            if ($module = App::module($package->get('module'))) {

                if ($settings = $module->get('settings') and $settings[0] === '@') {
                    $settings = App::url($settings);
                }

                $package->set('enabled', true);
                $package->set('settings', $settings);
                $package->set('config', $module->config);
                $package->set('permissions', (bool)$module->get('permissions'));
            }
        }

        return [
            '$view' => [
                'title' => __('Extensions'),
                'name' => 'installer:views/extensions.php'
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

        App::module()->load($package->get('module'));

        if (!$module = App::module($package->get('module'))) {
            App::abort(400, __('Unable to enable "%name%".', ['%name%' => $package->get('title')]));
        }

        App::trigger('enable', [$module]);
        App::trigger("enable.{$module->name}", [$module]);

        if ($package->getType() == 'pagekit-theme') {
            App::config('system')->set('site.theme', $module->name);
        } elseif ($package->getType() == 'pagekit-extension') {
            App::config('system')->push('extensions', $module->name);
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

        if (!$module = App::module($package->get('module'))) {
            App::abort(400, __('"%name%" has not been loaded.', ['%name%' => $package->get('title')]));
        }

        App::trigger('disable', [$module]);
        App::trigger("disable.{$module->name}", [$module]);

        if ($package->getType() == 'pagekit-extension') {
            App::config('system')->pull('extensions', $module->name);
        }

        App::module('system/cache')->clearCache();

        return ['message' => 'success'];
    }

    /**
     * @Request({"type": "string"}, csrf=true)
     */
    public function uploadAction($type)
    {
        $file = App::request()->files->get('file');

        if ($file === null || !$file->isValid()) {
            App::abort(400, __('No file uploaded.'));
        }

        $package = $this->loadPackage($file->getPathname());

        if (!$package->getName() || !$package->get('title') || !$package->get('version')) {
            App::abort(400, __('"composer.json" file not valid.'));
        }

        if ($package->get('type') !== 'pagekit-' . $type) {
            App::abort(400, __('No Pagekit %type%', ['%type%' => $type]));
        }

        $filename = str_replace('/', '-', $package->getName()) . '-' . $package->get('version') . '.zip';

        $file->move(App::get('path') . '/tmp/packages', $filename);

        return compact('package');
    }

    /**
     * @Request({"package": "array"}, csrf=true)
     */
    public function installAction($package = [])
    {
        return App::response()->stream(function () use ($package) {

            try {

                $package = App::package()->load($package);
                $name = $package->getName();

                if ($enabled = (bool)App::module($package->get('module'))) {
                    $this->disableAction($name);
                }

                $installer = new Installer();
                $installer->install([$package->getName() => $package->get('version')]);

                if ($enabled) {
                    $this->enableAction($name);
                }

                echo 'status=success';

            } catch (\Exception $e) {

                printf("%s\nstatus=error", $e->getMessage());
            }

        });
    }

    /**
     * @Request({"name"}, csrf=true)
     */
    public function uninstallAction($name)
    {
        return App::response()->stream(function () use ($name) {

            try {

                if (!$package = App::package($name)) {
                    throw new \Exception(__('Unable to find "%name%".', ['%name%' => $name]));
                }

                if (!App::module($package->get('module'))) {
                    App::module()->load($package->get('module'));
                }

                if (!$module = App::module($package->get('module'))) {
                    throw new \Exception(__('Unable to uninstall "%name%".', ['%name%' => $package->get('title')]));
                }

                $this->disableAction($name);

                App::trigger('uninstall', [$module]);
                App::trigger("uninstall.{$module->name}", [$module]);

                $installer = new Installer();
                $installer->uninstall([$package->getName()]);

                echo 'status=success';

            } catch (\Exception $e) {

                printf("%s\nstatus=error", $e->getMessage());
            }

        });
    }

    protected function loadPackage($file)
    {
        try {

            if (is_file($file)) {

                $zip = new \ZipArchive;

                if ($zip->open($file) === true) {
                    $json = $zip->getFromName('composer.json');

                    if ($json) {
                        $package = App::package()->load($json);
                        $extra = $package->get('extra');

                        if (isset($extra['image'])) {
                            unset($extra['image']);
                            $package->set('extra', $extra);
                        }

                        $package->set('shasum', sha1_file($file));
                    }

                    $zip->close();
                }
            }

            if (isset($package)) {
                return $package;
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
                $message .= '<br><br>' . $exception->getMessage();
            }

            App::response()->json($message, 500)->send();
        });
    }
}
