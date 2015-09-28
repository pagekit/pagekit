<?php

namespace Pagekit\Installer\Package;

use Pagekit\Application as App;
use Pagekit\Filesystem\Filesystem;
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

        $config = array_flip(['path.temp', 'path.cache', 'path.vendor', 'path.artifact', 'path.packages', 'system.api']);
        array_walk($config, function (&$value, $key) {
            $value = App::get($key);
        });

        $this->composer = new Composer($config, $output);
    }

    /**
     * @param array $install
     * @return bool
     */
    public function install(array $install = [])
    {
        $this->composer->install($install);

        $packages = App::package()->all(null, true);
        foreach ($install as $name => $version) {
            if (isset($packages[$name]) && App::module($packages[$name]->get('module'))) {
                $this->enable($packages[$name]);
            } elseif (isset($packages[$name])) {
                $this->doInstall($packages[$name]);
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

            $this->disable($package);
            $this->trigger('uninstall', $this->loadScripts($package));
            App::config('system')->remove('packages.' . $package->get('module'));

            if ($this->composer->isInstalled($package->getName())) {
                $this->composer->uninstall($package->getName());
            } else {
                if (!$path = $package->get('path')) {
                    throw new \RuntimeException(__('Package path is missing.'));
                }

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
        $scripts = $this->loadScripts($package);

        if (!($current = App::module('system')->config('packages.' . $package->get('module')))) {
            $current = $this->doInstall($package);
        }

        if (isset($scripts['updates'])) {
            $updates = $this->filterUpdates($scripts['updates'], $current);
            $this->execute($updates);
        }

        $version = $this->getVersion($package);
        App::config('system')->set('packages.' . $package->get('module'), $version);

        $this->trigger('enable', $this->loadScripts($package));

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
        $this->trigger($package, 'disable');

        if ($package->getType() == 'pagekit-extension') {
            App::config('system')->pull('extensions', $package->get('module'));
        }
    }

    /**
     * @param $updates
     * @param $current
     * @return array
     */
    public function filterUpdates($updates, $current)
    {
        $updates = array_filter($updates, function () use (&$updates, $current) {
            return version_compare(key($updates), $current, '>');
        });
        uksort($updates, 'version_compare');

        return $updates;
    }

    /**
     * @param array|callable $scripts
     */
    public function execute($scripts)
    {
        array_map(function ($script) {
            call_user_func($script, App::getInstance());
        }, (array)$scripts);
    }

    /**
     * @param $name
     * @param $scripts
     */
    public function trigger($name, $scripts)
    {
        if (isset($scripts[$name]) && is_callable($func = $scripts[$name])) {
            $this->execute($func);
        }
    }

    /**
     * @param $package
     * @param $path
     * @return array
     */
    public function loadScripts($package, $path = null)
    {
        if (!$path) {
            if (!($extra = $package->get('extra')) || !isset($extra['scripts'])) {
                return [];
            }

            if (!$path = $package->get('path')) {
                throw new \RuntimeException(__('Package path is missing.'));
            }

            $path = $path . '/' . $extra['scripts'];
        }

        return file_exists($path) ? require $path : [];
    }

    /**
     * @param $package
     * @return string
     */
    protected function doInstall($package)
    {
        $this->trigger('install', $this->loadScripts($package));
        $version = $this->getVersion($package);

        App::config('system')->set('packages.' . $package->get('module'), $version);

        return $version;
    }

    /**
     * Tries to obtain package version from 'composer.json' or installation log.
     *
     * @param $package
     * @return string
     */
    protected function getVersion($package)
    {
        if (!$path = $package->get('path')) {
            throw new \RuntimeException(__('Package path is missing.'));
        }

        if (!file_exists($file = $path . '/composer.json')) {
            throw new \RuntimeException(__('\'composer.json\' is missing.'));
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

        return '0.0.0';
    }
}
