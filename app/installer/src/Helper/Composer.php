<?php

namespace Pagekit\Installer\Helper;

use Pagekit\Application as App;
use Composer\Factory;
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
    protected static $io;

    /**
     * @var OutputInterface
     */
    protected static $output;

    /**
     * @var Composer
     */
    protected static $composer;

    /**
     * @var array
     */
    protected static $packages = [];

    /**
     * @var string
     */
    protected static $file = 'packages.php';

    /**
     * @var string
     */
    protected static $config = [
        'repositories' => [
            ['type' => 'artifact', 'url' => 'tmp/packages/'],
            ['type' => 'composer', 'url' => 'http://pagekit.com']
        ],
    ];

    /**
     * @param array $install [name => version, name => version, ...]
     * @param OutputInterface $output
     * @return bool
     */
    public static function install(array $install, $output = null)
    {
        self::setOutput($output);
        self::addPackages($install);

        self::composerUpdate(array_keys($install));
        self::writeConfig();
    }


    /**
     * @param array|string $uninstall [name, name, ...]
     * @param OutputInterface $output
     */
    public static function uninstall($uninstall, $output = null)
    {
        self::setOutput($output);
        $uninstall = (array)$uninstall;

        self::removePackages($uninstall);

        self::composerUpdate($uninstall);
        self::writeConfig();
    }

    /**
     * Checks if a package is installed by composer.
     *
     * @param $name
     * @return bool
     */
    public static function isInstalled($name)
    {
        $installed = App::get('path.packages') . '/composer/installed.json';
        $installed = file_exists($installed) ? json_decode(file_get_contents($installed), true) : [];

        $installed = array_map(function ($pkg) {
            return $pkg['name'];
        }, $installed);

        return array_search($name, $installed) !== false;
    }

    /**
     * @param OutputInterface $output
     */
    public static function setOutput($output)
    {
        self::$output = $output;
    }

    /**
     * Runs Composer Update command.
     *
     * @param  array|bool $updates
     * @return bool
     */
    protected static function composerUpdate($updates = false)
    {
        $installed = new JsonFile(App::get('path.vendor') . '/composer/installed.json');
        $internal = new CompositeRepository([]);
        $internal->addRepository(new InstalledFilesystemRepository($installed));

        $composer = self::getComposer();

        $installer = Installer::create(self::getIO(), $composer)
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
    protected static function getComposer()
    {
        chdir(App::path());

        if (!self::$composer) {
            putenv('COMPOSER_HOME=' . App::get('path.temp') . '/composer');
            putenv('COMPOSER_VENDOR_DIR=' . App::get('path.packages'));

            // set memory limit, if < 512M
            $memory = trim(ini_get('memory_limit'));
            if ($memory != -1 && self::memoryInBytes($memory) < 512 * 1024 * 1024) {
                @ini_set('memory_limit', '512M');
            }

            $config = self::$config;
            $config['require'] = self::$packages;

            $composer = Factory::create(self::getIO(), $config);
            $composer->setLocker(new Locker(
                self::getIO(),
                new JsonFile(preg_replace('/\.php$/i', '.lock', App::get('path.packages') . '/' . self::$file)),
                $composer->getRepositoryManager(),
                $composer->getInstallationManager(),
                md5(json_encode($config))
            ));

            self::$composer = $composer;
        }

        return self::$composer;
    }


    /**
     * @return InstallerIO
     */
    protected static function getIO()
    {
        return self::$io ?: (self::$io = new InstallerIO(null, self::$output));
    }

    /**
     * @param $packages
     * @return array
     */
    protected static function addPackages($packages)
    {
        self::$packages = array_merge(self::readConfig(), $packages);
    }

    /**
     * @param $packages
     * @return array
     */
    protected static function removePackages($packages)
    {
        self::$packages = array_diff_key(self::readConfig(), array_flip($packages));
    }

    /**
     * Reads packages from package file.
     *
     * @return array
     */
    protected static function readConfig()
    {
        return file_exists($path = App::get('path.packages') . '/' . self::$file) ? require $path : [];
    }

    /**
     * Writes changes to packages file.
     */
    protected static function writeConfig()
    {
        file_put_contents(App::get('path.packages') . '/' . self::$file, '<?php return ' . var_export(self::$packages, true) . ';');
    }

    /**
     * Converts memory value from 'php.ini' into bytes.
     *
     * @param $value
     * @return int
     */
    protected static function memoryInBytes($value)
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
