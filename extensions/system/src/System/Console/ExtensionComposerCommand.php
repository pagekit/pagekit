<?php

namespace Pagekit\System\Console;

use Pagekit\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Factory;
use Composer\Installer;
use Composer\Json\JsonFile;
use Composer\IO\ConsoleIO;
use Composer\Package\Locker;
use Composer\Repository\CompositeRepository;
use Composer\Repository\InstalledFilesystemRepository;


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
    protected $description = 'Extension Composer';

    /**
     * Executes composer for the extension.
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $name   = $this->argument('extension');
        $update = $this->option('update');
        $io     = new ConsoleIO($input, $output, $this->getHelperSet());

        if (!is_dir($path = $this->pagekit['path.extensions']."/$name") && file_exists("$path/extension.json")) {
            $this->error("Extension not exists '$path'");
            exit;
        }

        $package = json_decode(file_get_contents("$path/extension.json"), true);

        if (!isset($package['composer']) || empty($package['composer'])) {
            $this->error("Composer not defined in '$path/extension.json'");
            exit;
        }

        // Create Composer
        putenv('COMPOSER_HOME='.$this->pagekit['path.vendor'].'/composer/composer');
        putenv('COMPOSER_CACHE_DIR='.$this->pagekit['path.cache'].'/composer');
        putenv('COMPOSER_VENDOR_DIR='.$path.'/vendor');

        $composer = Factory::create($io, $package['composer']);
        $lockFile = new JsonFile("$path/extension.lock");
        $locker   = new Locker($io, $lockFile, $composer->getRepositoryManager(), $composer->getInstallationManager(), md5(json_encode($package['composer'])));
        $composer->setLocker($locker);

        // Create Installer
        $installer                   = Installer::create($io, $composer);
        $internalRepository          = new CompositeRepository(array());
        $installed                   = new JsonFile($this->pagekit['path'].'/vendor/composer/installed.json');
        $internalInstalledRepository = new InstalledFilesystemRepository($installed);

        $internalRepository->addRepository($internalInstalledRepository);
        $installer->setAdditionalInstalledRepository($internalRepository);
        $installer->setUpdate($update);

        return $installer->run();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument('extension', InputArgument::REQUIRED, 'Extension name');
        $this->addOption('update', 'u', InputOption::VALUE_NONE, 'Update composer');

    }
}
