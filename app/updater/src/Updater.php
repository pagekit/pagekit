<?php

namespace Pagekit\Updater;

use Composer\Console\Application as ComposerApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class Updater
{
    const packagesFile = '/packages.json';

    /**
     *
     * @param array $config
     * @param OutputInterface $output
     */
    public function __construct($config, OutputInterface $output)
    {
        $this->output = $output;
        $this->pagekitConfig = $config;
        $this->packagesFile = $this->pagekitConfig['values']['path'] . self::packagesFile;
        $this->packages = file_exists($this->packagesFile) ? json_decode(file_get_contents($this->packagesFile), true) : [];

        putenv('COMPOSER_HOME=' . $config['values']['path.temp']);
        putenv('COMPOSER_CACHE_DIR=' . $config['values']['path.cache'] . '/composer');
        putenv('COMPOSER_VENDOR_DIR=' . $config['values']['path'] . '/vendor');

        // set memory limit, if < 512M
        $memory = trim(ini_get('memory_limit'));
        if ($memory != -1 && $this->memoryInBytes($memory) < 512 * 1024 * 1024) {
            @ini_set('memory_limit', '512M');
        }
    }

    /**
     * @param array $arguments
     */
    public function run($arguments)
    {
        $packages = isset($arguments['packages']) ? $this->parsePackages($arguments['packages']) : [];

        if ($packages && !isset($arguments['remove'])) {
            $this->addRequirements($packages);
            return;
        }

        if ($packages && isset($arguments['remove'])) {
            $this->removeRequirements($packages);
            return;
        }

        $this->composerUpdate();
    }

    /**
     * Parses version constraints from package names.
     *
     * @param array $arguments
     * @return array
     */
    protected function parsePackages($arguments)
    {
        $packages = [];
        foreach ((array)$arguments as $argument) {
            $argument = explode(':', $argument);
            $packages[] = ['name' => $argument[0], 'version' => isset($argument[1]) ? $argument[1] : '*'];
        }

        return $packages;
    }

    /**
     * Installs new packages.
     *
     * @param $packages
     * @throws \Exception
     */
    protected function addRequirements($packages)
    {
        $update = [];
        foreach ($packages as $package) {
            if (preg_match('/^[\w\d\-_]+\/[\w\d\-_]+\z/', $package['name'])) {
                $update[] = $this->handleRepository($package);
            }
        }

        if ($this->composerUpdate($update)) {
            $this->writePackagesFile();
        }
    }

    /**
     * Uninstalls packages.
     *
     * @param $packages
     */
    protected function removeRequirements($packages)
    {
        foreach ($packages as $package) {
            unset($this->packages[$package['name']]);
        }

        $this->writePackagesFile();
        $this->composerUpdate(array_map(function ($package) {
            return $package['name'];
        }, $packages));
    }

    /**
     * Handles packages from remote repository.
     *
     * @param $package
     * @return array
     */
    protected function handleRepository($package)
    {
        $this->packages[$package['name']] = $package['version'];

        return $package['name'];
    }

    /**
     * Writes changes to packages file.
     */
    protected function writePackagesFile()
    {
        file_put_contents($this->packagesFile, json_encode($this->packages, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT));
    }

    /**
     * Runs Composer Update command.
     *
     * @param array|false $packages
     */
    protected function composerUpdate($packages = false)
    {
        global $pagekit_packages;

        $pagekit_packages = $this->packages;

        $params = ['update', '--prefer-dist'];
        $params['--working-dir'] = $this->pagekitConfig['values']['path'];
        if ($packages) {
            $params['packages'] = $packages;
        }

        $composer = new ComposerApplication();
        $composer->setAutoExit(false);
        $composer->run(new ArrayInput($params), $this->output);

        return empty($this->output->getError());
    }

    /**
     * Converts memory value from 'php.ini' into bytes.
     *
     * @param $value
     * @return int
     */
    protected function memoryInBytes($value)
    {
        $unit = strtolower(substr($value, -1, 1));
        $value = (int)$value;

        switch ($unit) {
            case 'g':
                $value *= 1024;
            // no break (cumulative multiplier)
            case 'm':
                $value *= 1024;
            // no break (cumulative multiplier)
            case 'k':
                $value *= 1024;
        }

        return $value;
    }
}