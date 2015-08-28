<?php

namespace Pagekit\Console\Commands;

use Pagekit\Application\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Pagekit\Application as App;

class BuildCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'build';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Builds a .zip release file.';

    /**
    * @var string[]
    */
   protected $excludes = [
       '^(app\/database|tmp\/cache|tmp\/logs|tmp\/sessions|tmp\/temp|storage|config\.php|pagekit.+\.zip)',
       '^extensions\/.+\/languages\/.+\.(po|pot)',
       '^themes\/(?!(one)\/).*',
       'node_modules',
       '^vendor\/(.*)\/(build|bin|doc|docs|examples|grammar|test|tests|test_old|phpunit|notes|run)\/',
       '(run|makefile|composer\.lock)$',
   ];

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $info   = $this->container->info()->get();
        $filter = '/'.implode('|', $this->excludes).'/i';

        $zip    = new \ZipArchive;
        $root   = __DIR__.'/../../../../';
        $path   = $root;
        $vers   = $info['version'];

        $finder = Finder::create()->files()->in($root)->ignoreVCS(true)->filter(function ($file) use($filter) {
            return !preg_match($filter, $file->getRelativePathname());
        });

        $zip = new \ZipArchive;

        if (!$zip->open($zipFile = "{$path}/pagekit-".$vers.".zip", \ZipArchive::OVERWRITE)) {
            $this->abort("Can't open ZIP extension in '{$zipFile}'");
        }

        foreach ($finder as $file) {
            $zip->addFile($file->getPathname(), $file->getRelativePathname());
        }

        $zip->addEmptyDir('tmp/');
        $zip->addEmptyDir('tmp/cache');
        $zip->addEmptyDir('tmp/temp');
        $zip->addEmptyDir('tmp/logs');
        $zip->addEmptyDir('tmp/sessions');
        $zip->addEmptyDir('tmp/packages');
        $zip->addEmptyDir('app/database');
        $zip->addEmptyDir('storage');

        $zip->close();

        $name = basename($zipFile);
        $size = filesize($zipFile) / 1024 / 1024;

        $this->line(sprintf('Building: %s (%.2f MB)', $name, $size));
    }
}
