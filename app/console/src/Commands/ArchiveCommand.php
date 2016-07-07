<?php

namespace Pagekit\Console\Commands;

use Composer\Json\JsonFile;
use Composer\Package\Archiver\PharArchiver;
use Composer\Util\Filesystem;
use Pagekit\Application\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ArchiveCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'archive';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Archives an extension or theme';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Package name');
        $this->addOption('dir', false, InputOption::VALUE_OPTIONAL, 'Write the archive to this directory');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filesystem = new Filesystem();
        $packageName = $this->getPackageFilename($name = $this->argument('name'));

        if (!($targetDir = $this->option('dir'))) {
            $targetDir = $this->container->path();
        }

        $sourcePath = $this->container->get('path.packages').'/'.$name;

        $filesystem->ensureDirectoryExists($targetDir);

        $target = realpath($targetDir).'/'.$packageName.'.zip';
        $filesystem->ensureDirectoryExists(dirname($target));

        $excludes = [];
        if (file_exists($composerJsonPath = $sourcePath.'/composer.json')) {
            $jsonFile = new JsonFile($composerJsonPath);
            $jsonData = $jsonFile->read();

            if (!empty($jsonData['archive']['exclude'])) {
                $excludes = ($jsonData['archive']['exclude']);
            }

            if (!empty($jsonData['archive']['scripts'])) {
                system($jsonData['archive']['scripts'], $return);

                if ($return !== 0) {
                    throw new \RuntimeException('Can not executes scripts.');
                }
            }
        }

        $tempTarget = sys_get_temp_dir().'/composer_archive'.uniqid().'.zip';
        $filesystem->ensureDirectoryExists(dirname($tempTarget));

        $archivePath = (new PharArchiver())->archive($sourcePath, $tempTarget, 'zip', $excludes);
        rename($archivePath, $target);

        $filesystem->remove($tempTarget);

        return $target;
    }

    protected function getPackageFilename($name)
    {
        // TODO: Make this more robust.
        return preg_replace('#[^a-z0-9-_]#i', '-', $name);
    }
}
