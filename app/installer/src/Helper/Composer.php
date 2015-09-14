<?php

namespace Pagekit\Installer\Helper;

use Composer\Installer;
use Composer\Json\JsonFile;
use Composer\Package\Locker;
use Composer\Repository\CompositeRepository;
use Composer\Repository\InstalledFilesystemRepository;
use Symfony\Component\Console\Output\OutputInterface;


class Composer
{
    /**
     * @var ConsoleIO
     */
    protected $io;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var array
     */
    protected $packages = [];

    /**
     * @var string
     */
    protected $file = 'packages.php';

    /**
     * @param $config
     * @param $api
     * @param null $output
     */
    public function __construct($config, $output = null)
    {
        $this->paths = $config;
        $this->output = $output;

        $this->file = $config['path.packages'] . '/' . $this->file;
        $this->blueprint = [
            'repositories' => [
                ['type' => 'artifact', 'url' => $config['path.artifact']],
                ['type' => 'composer', 'url' => $config['system.api']]
            ]
        ];
    }

    /**
     * @param array $install [name => version, name => version, ...]
     * @return bool
     */
    public function install(array $install)
    {
        $this->addPackages($install);

        $this->composerUpdate(array_keys($install));
        $this->writeConfig();
    }


    /**
     * @param array|string $uninstall [name, name, ...]
     */
    public function uninstall($uninstall)
    {
        $uninstall = (array)$uninstall;

        $this->removePackages($uninstall);

        $this->composerUpdate($uninstall);
        $this->writeConfig();
    }

    /**
     * Checks if a package is installed by composer.
     *
     * @param $name
     * @return bool
     */
    public function isInstalled($name)
    {
        $installed = $this->paths['path.packages'] . '/composer/installed.json';
        $installed = file_exists($installed) ? json_decode(file_get_contents($installed), true) : [];

        $installed = array_map(function ($pkg) {
            return $pkg['name'];
        }, $installed);

        return array_search($name, $installed) !== false;
    }

    /**
     * Runs Composer Update command.
     *
     * @param  array|bool $updates
     * @return bool
     */
    protected function composerUpdate($updates = false)
    {
        $installed = new JsonFile($this->paths['path.vendor'] . '/composer/installed.json');
        $internal = new CompositeRepository([]);
        $internal->addRepository(new InstalledFilesystemRepository($installed));

        $composer = $this->getComposer();

        $installer = Installer::create($this->getIO(), $composer)
            ->setAdditionalInstalledRepository($internal)
            ->setPreferDist(true)
            ->setOptimizeAutoloader(true)
            ->setUpdate(true);

        if ($updates) {
            $installer->setUpdateWhitelist($updates);
        }

        $installer->run();
    }

    /**
     * Returns composer instance.
     *
     * @return null
     */
    protected function getComposer()
    {
        $config = $this->blueprint;
        $config['config']  = ['vendor-dir' => $this->paths['path.packages']];
        $config['require'] = $this->packages;

        // set memory limit, if < 512M
        $memory = trim(ini_get('memory_limit'));
        if ($memory != -1 && $this->memoryInBytes($memory) < 512 * 1024 * 1024) {
            @ini_set('memory_limit', '512M');
        }

        Factory::setHomeDir($this->paths['path.temp'] . '/composer');
        $composer = Factory::create($this->getIO(), $config);
        $composer->setLocker(new Locker(
            $this->getIO(),
            new JsonFile(preg_replace('/\.php$/i', '.lock', $this->file)),
            $composer->getRepositoryManager(),
            $composer->getInstallationManager(),
            md5(json_encode($config))
        ));

        return $composer;
    }


    /**
     * @return InstallerIO
     */
    protected function getIO()
    {
        return $this->io ?: ($this->io = new InstallerIO(null, $this->output));
    }

    /**
     * @param $packages
     * @return array
     */
    protected function addPackages($packages)
    {
        $this->packages = array_merge($this->readConfig(), $packages);
    }

    /**
     * @param $packages
     * @return array
     */
    protected function removePackages($packages)
    {
        $this->packages = array_diff_key($this->readConfig(), array_flip($packages));
    }

    /**
     * Reads packages from package file.
     *
     * @return array
     */
    protected function readConfig()
    {
        return file_exists($this->file) ? require $this->file : [];
    }

    /**
     * Writes changes to packages file.
     */
    protected function writeConfig()
    {
        file_put_contents($this->file, '<?php return ' . var_export($this->packages, true) . ';');
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
