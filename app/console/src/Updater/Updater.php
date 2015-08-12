<?php

namespace Pagekit\Console\Updater;

use Composer\Factory;
use Composer\Installer;
use Composer\IO\ConsoleIO;
use Composer\Json\JsonFile;
use Composer\Package\Locker;
use Composer\Repository\CompositeRepository;
use Composer\Repository\InstalledFilesystemRepository;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Updater
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

    protected $packagesConfig = [
        'repositories' => [
            [
                'type' => 'artifact',
                'url' => 'tmp/packages/'
            ],
            [
                'type' => 'composer',
                'url' => 'http://pagekit.com'
            ]
        ],
        'require' => [
            'composer/installers' => '1.0.22'
        ]
    ];

    /**
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->file = $config['path'] . '/' . self::CONFIG_FILE;
        $this->packages = $this->readPackages();

        chdir($config['path']);

        putenv('COMPOSER_HOME=' . $config['path']);
        putenv('COMPOSER_CACHE_DIR=' . $config['path.temp'] . '/composer');
        putenv('COMPOSER_VENDOR_DIR=' . $config['path'] . '/vendor/packages');

        // set memory limit, if < 512M
        $memory = trim(ini_get('memory_limit'));
        if ($memory != -1 && $this->memoryInBytes($memory) < 512 * 1024 * 1024) {
            @ini_set('memory_limit', '512M');
        }
    }

    /**
     * @param $input
     * @param $output
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $packages = $this->parsePackages($input->getArgument('packages'));

        if ($packages && !($input->hasOption('remove') && $input->getOption('remove'))) {
            $this->addRequirements($packages);
            return;
        }

        if ($packages && ($input->hasOption('remove') && $input->getOption('remove'))) {
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
        foreach ((array)$arguments as $argument) {
            $argument = explode(':', $argument);
            $packages[] = ['name' => $argument[0], 'version' => isset($argument[1]) && $argument[1] ? $argument[1] : '*'];
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
        $packagesConfig = $this->packagesConfig;
        $packagesConfig['require'] = array_merge($packagesConfig['require'], $this->packages);

        $io = new ConsoleIO(new ArrayInput([]), $this->output, new HelperSet());
        $composer = Factory::create($io, $packagesConfig);

        $lockFile = new JsonFile($this->config['path'] . '/packages.lock');
        $locker = new Locker($io, $lockFile, $composer->getRepositoryManager(), $composer->getInstallationManager(), md5(json_encode($packagesConfig)));
        $composer->setLocker($locker);

        $installed = new JsonFile($this->config['path'] . '/vendor/composer/installed.json');
        $internal = new CompositeRepository([]);
        $internal->addRepository(new InstalledFilesystemRepository($installed));

        $installer = Installer::create($io, $composer)
            ->setAdditionalInstalledRepository($internal)
            ->setPreferDist(true)
            ->setOptimizeAutoloader(true)
            ->setUpdate(true);

        if ($updates) {
            $installer->setUpdateWhitelist($updates);
        }

        $installer->run();

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
