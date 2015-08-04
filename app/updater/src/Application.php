<?php

namespace Pagekit\Updater;

use Composer\Console\Application as Composer;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class Application
{
    const CONFIG_FILE = 'packages.json';

    /**
     * @var array
     */
    protected $config;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var string
     */
    protected $file;

    /**
     * @param array           $config
     * @param OutputInterface $output
     */
    public function __construct($config, OutputInterface $output)
    {
        $this->config   = $config;
        $this->output   = $output;
        $this->file     = $this->config['path'].'/'.self::CONFIG_FILE;
        $this->packages = $this->readPackages();

        putenv('COMPOSER_HOME='.$config['path.temp']);
        putenv('COMPOSER_CACHE_DIR='.$config['path.cache'].'/composer');
        putenv('COMPOSER_VENDOR_DIR='.$config['path'].'/vendor');

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
     * @param  array $arguments
     * @return array
     */
    protected function parsePackages($arguments)
    {
        $packages = [];
        foreach ($arguments as $argument) {
            $argument   = explode(':', $argument);
            $packages[] = ['name' => $argument[0], 'version' => isset($argument[1]) ? $argument[1] : '*'];
        }

        return $packages;
    }

    /**
     * Reads packages from package file.
     *
     * @return array
     */
    protected function readPackages()
    {
        return file_exists($this->file) ? json_decode(file_get_contents($this->file), true) : [];
    }

    /**
     * Writes changes to packages file.
     */
    protected function writePackages()
    {
        file_put_contents($this->file, json_encode($this->packages, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT));
    }

    /**
     * Installs new packages.
     *
     * @param  $packages
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
            $this->writePackages();
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

        $this->writePackages();
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
     * Runs Composer Update command.
     *
     * @param  array|bool $updates
     * @return bool
     */
    protected function composerUpdate($updates = false)
    {
        global $packages;

        $packages = $this->packages;

        $params                  = ['update', '--prefer-dist'];
        $params['--working-dir'] = $this->config['path'];
        if ($updates) {
            $params['packages'] = $updates;
        }

        $composer = new Composer();
        $composer->setAutoExit(false);
        $composer->run(new ArrayInput($params), $this->output);

        $error = $this->output->getError();

        return empty($error);
    }

    /**
     * Converts memory value from 'php.ini' into bytes.
     *
     * @param $value
     * @return int
     */
    protected function memoryInBytes($value)
    {
        $unit  = strtolower(substr($value, -1, 1));
        $value = (int) $value;

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
