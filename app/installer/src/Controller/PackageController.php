<?php

namespace Pagekit\Installer\Controller;

use Pagekit\Application as App;
use Pagekit\Installer\Package\PackageManager;

/**
 * @Access("system: manage packages", admin=true)
 */
class PackageController
{
    public function themesAction()
    {
        $packages = array_values(App::package()->all('pagekit-theme'));

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
        $packages = array_values(App::package()->all('pagekit-extension'));

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
        $package = App::package()->get($name);

        $manager = new PackageManager();
        $manager->enable($package);

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

                $manager = new PackageManager();
                $manager->install([$package->getName() => $package->get('version')]);

                echo "\nstatus=success";

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

                $manger = new PackageManager();
                $manger->uninstall($name);

                echo "\nstatus=success";

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
}
