<?php

namespace Pagekit\Installer;

use Pagekit\Application as App;
use Composer\Factory;
use Composer\Installer as ComposerInstaller;
use Composer\Json\JsonFile;
use Composer\Package\Locker;
use Composer\Repository\CompositeRepository;
use Composer\Repository\InstalledFilesystemRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Installer
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var ConsoleIO
     */
    protected $io;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var string
     */
    protected $config = [
        'repositories' => [
            ['type' => 'artifact', 'url' => 'tmp/packages/'],
            ['type' => 'composer', 'url' => 'http://pagekit.com']
        ],
    ];

    /**
     * Constructor.
     *
     * @param string $path
     * @param mixed $output
     */
    public function __construct($output = null)
    {
        $this->file = App::get('path.packages') . '/packages.php';
        $this->output = $output;
        $this->packages = $this->readPackages();

        chdir(App::path());

        putenv('COMPOSER_HOME=' . App::path());
        putenv('COMPOSER_CACHE_DIR=' . App::get('path.temp') . '/composer');
        putenv('COMPOSER_VENDOR_DIR=' . App::get('path.packages'));

        // set memory limit, if < 512M
        $memory = trim(ini_get('memory_limit'));
        if ($memory != -1 && $this->memoryInBytes($memory) < 512 * 1024 * 1024) {
            @ini_set('memory_limit', '512M');
        }
    }

    /**
     * @param array $install
     * @return bool
     */
    public function install(array $install = [])
    {
        $this->packages = array_merge($this->packages, $install);
        $this->composerUpdate(array_keys($install));

        $this->writePackages();
    }

    /**
     * @param array $uninstall
     * @return bool
     */
    public function uninstall(array $uninstall)
    {
        $this->packages = array_diff_key($this->packages, array_flip($uninstall));

        $this->writePackages();

        return $this->composerUpdate($uninstall);
    }

    /**
     * Reads packages from package file.
     *
     * @return array
     */
    protected function readPackages()
    {
        return file_exists($this->file) ? require $this->file : [];
    }

    /**
     * Writes changes to packages file.
     */
    protected function writePackages()
    {
        file_put_contents($this->file, '<?php return ' . var_export($this->packages, true) . ';');
    }

    /**
     * Runs Composer Update command.
     *
     * @param  array|bool $updates
     * @return bool
     */
    protected function composerUpdate($updates = false)
    {
        $config = $this->config;
        $config['require'] = $this->packages;

        $io = new InstallerIO($this->input, $this->output);
        $composer = Factory::create($io, $config);

        $lockFile = new JsonFile(preg_replace('/\.php$/i', '.lock', $this->file));
        $locker = new Locker($io, $lockFile, $composer->getRepositoryManager(), $composer->getInstallationManager(), md5(json_encode($config)));
        $composer->setLocker($locker);

        $installed = new JsonFile(App::get('path.vendor') . '/composer/installed.json');
        $internal = new CompositeRepository([]);
        $internal->addRepository(new InstalledFilesystemRepository($installed));

        $installer = ComposerInstaller::create($io, $composer)
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
