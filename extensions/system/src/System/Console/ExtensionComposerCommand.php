<?php

namespace Pagekit\System\Console;

use Composer\Factory;
use Composer\Installer;
use Composer\Json\JsonFile;
use Composer\IO\ConsoleIO;
use Composer\Package\Locker;
use Composer\Repository\CompositeRepository;
use Composer\Repository\InstalledFilesystemRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Pagekit\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExtensionComposerCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extension:composer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the extension dependencies';

    /**
     * Executes composer for the extension.
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $name   = $this->argument('extension');
        $update = $this->option('update');

        if (!is_dir($path = $this->pagekit['path.extensions']."/$name") && file_exists("$path/extension.json")) {
            $this->error("Extension not exists '$path'");
            exit;
        }

        $package = json_decode(file_get_contents("$path/extension.json"), true);

        if (!isset($package['composer']) || empty($package['composer'])) {
            $this->error("Composer not defined in '$path/extension.json'");
            exit;
        }

        $this->loadComposer($path);

        $io       = new ConsoleIO($input, $output, $this->getHelperSet());
        $composer = Factory::create($io, $package['composer']);
        $lockFile = new JsonFile("$path/extension.lock");
        $locker   = new Locker($io, $lockFile, $composer->getRepositoryManager(), $composer->getInstallationManager(), md5(json_encode($package['composer'])));
        $composer->setLocker($locker);

        $installed = new JsonFile($this->pagekit['path'].'/vendor/composer/installed.json');
        $internal  = new CompositeRepository([]);
        $internal->addRepository(new InstalledFilesystemRepository($installed));

        $installer = Installer::create($io, $composer);
        $installer->setAdditionalInstalledRepository($internal);
        $installer->setUpdate($update);

        return $installer->run();
    }

    /**
     * Loads the composer from .phar archive.
     *
     * @param string $path
     */
    protected function loadComposer($path)
    {
        $composer = $this->pagekit['path.temp'].'/composer.phar';
        $memory   = trim(ini_get('memory_limit'));

        // set environment
        putenv('COMPOSER_HOME='.$this->pagekit['path.temp']);
        putenv('COMPOSER_CACHE_DIR='.$this->pagekit['path.cache'].'/composer');
        putenv('COMPOSER_VENDOR_DIR='.$path.'/vendor');

        // set memory limit, if < 512M
        if ($memory != -1 && $this->memoryInBytes($memory) < 512 * 1024 * 1024) {
            @ini_set('memory_limit', '512M');
        }

        // get composer.phar
        if (!file_exists($composer)) {

            $this->line('Downloading composer.phar ...');

            try {

                $client = new Client;

                file_put_contents($composer, $client->get('https://getcomposer.org/composer.phar')->getBody());

            } catch (BadResponseException $e) {
                $this->line(sprintf('Error: %s', $e->getMessage()));
            }
        }

        require "phar://{$composer}/src/bootstrap.php";
    }

    /**
     * Get memory in bytes.
     *
     * @param  string $value
     * @return int
     */
    protected function memoryInBytes($value)
    {

        $unit  = strtolower(substr($value, -1, 1));
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

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument('extension', InputArgument::REQUIRED, 'Extension name')
            ->addOption('update', 'u', InputOption::VALUE_NONE, 'Update composer');
    }
}
