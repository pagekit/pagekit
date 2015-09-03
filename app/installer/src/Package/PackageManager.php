<?php

namespace Pagekit\Installer\Package;

use Pagekit\Application as App;
use Pagekit\Installer\Helper\Composer;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

class PackageManager
{

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * Constructor.
     *
     * @param mixed $output
     */
    public function __construct($output = null)
    {
        $this->output = $output ?: new StreamOutput(fopen('php://output', 'w'));
    }

    /**
     * @param array $install
     * @return bool
     */
    public function install(array $install = [])
    {
        Composer::install($install, $this->output);

        $packages = App::package()->all(null, true);
        foreach ($install as $name => $version) {
            if (isset($packages[$name]) && App::module($packages[$name]->get('module'))) {
                $this->enable($packages[$name]);
            }
        }
    }

    /**
     * @param $uninstall
     * @return bool
     */
    public function uninstall($uninstall)
    {
        foreach ((array)$uninstall as $name) {
            if (!$package = App::package($name)) {
                throw new \RuntimeException(__('Unable to find "%name%".', ['%name%' => $name]));
            }

            if (!$path = $package->get('path')) {
                throw new \RuntimeException(__('Package path is missing.'));
            }
            $path = App::get('path.packages') . '/' . $path;

            $this->trigger($package, 'uninstall');
//            App::config('system')->pull('migration', $package->get('module'));

            if (Composer::isInstalled($package->getName())) {
                Composer::uninstall($package->getName(), $this->output);
            } else {

                $this->output->writeln(__("Removing package folder."));

                $file = new Filesystem;
                $file->delete($path);

                @rmdir(dirname($path));
            }
        }
    }

    /**
     * @param $package
     */
    public function enable($package)
    {
        $this->migrate($package);
        $this->trigger($package, 'enable');

        if ($package->getType() == 'pagekit-theme') {
            App::config('system')->set('site.theme', $package->get('module'));
        } elseif ($package->getType() == 'pagekit-extension') {
            App::config('system')->push('extensions', $package->get('module'));
        }
    }

    /**
     * @param $package
     */
    public function disable($package)
    {
        if ($package->getType() == 'pagekit-extension') {
            App::config('system')->pull('extensions', $package->get('module'));
        }
    }

    /**
     * @param $package
     */
    protected function migrate($package)
    {
        $scripts = $this->loadScripts($package);

        if (isset($scripts['migrations']) &&
            is_array($migrationList = $scripts['migrations']) &&
            $new = $this->getVersion($package)
        ) {
            $current = App::module('system')->config('migration.' . $package->get('module'));

            if (!$current) {
                $migrationList = array_intersect_key($migrationList, array_flip(['init']));
            } else {
                unset($migrationList['init']);
                $migrationList = array_filter($migrationList, function () use (&$migrationList, $current) {
                    return version_compare(key($migrationList), $current, '>');
                });
            }

            uksort($migrationList, 'version_compare');
            array_map(function ($migration) {
                call_user_func($migration, App::getInstance());
            }, $migrationList);

            App::config('system')->set('migration.' . $package->get('module'), $new);
        }
    }

    /**
     * Tries to obtain package version from 'composer.json' or installation log.
     *
     * @param $package
     * @return bool
     */
    protected function getVersion($package)
    {
        if (!$path = $package->get('path')) {
            throw new \RuntimeException(__('Package path is missing.'));
        }

        if (!file_exists($file = $path . '/composer.json')) {
            return false;
        }

        $package = json_decode(file_get_contents($file), true);
        if (isset($package['version'])) {
            return $package['version'];
        }

        if (file_exists(App::get('path.packages') . '/composer/installed.json')) {
            $installed = json_decode(file_get_contents($file), true);

            foreach ($installed as $package) {
                if ($package['name'] === $package->getName()) {
                    return $package['version'];
                }
            }
        }

        return false;
    }

    /**
     * @param $package
     * @param $script
     * @return mixed
     */
    protected function trigger($package, $script)
    {
        $scripts = $this->loadScripts($package);

        if (isset($scripts[$script]) && is_callable($func = $scripts[$script])) {
            return call_user_func($func, App::getInstance());
        }
    }

    /**
     * @param $package
     * @return array|mixed
     */
    protected function loadScripts($package)
    {
        if (!$path = $package->get('path')) {
            throw new \RuntimeException(__('Package path is missing.'));
        }

        return file_exists($path = $path . '/scripts.php') ? require $path : [];
    }
}
