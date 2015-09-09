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
       '^(app\/database|packages|tmp|config\.php|pagekit.+\.zip)',
       '^app\/assets\/[^\/]+\/(dist\/vue-.+\.js|dist\/jquery\.js|lodash\.js)',
       '^app\/assets\/(jquery|vue)\/(src|perf)',
       '^app\/vendor\/lusitanian\/oauth\/examples',
       '^app\/vendor\/maximebf\/debugbar\/src\/DebugBar\/Resources',
       '^app\/vendor\/nickic\/php-parser\/(grammar|test_old)',
       '^app\/vendor\/(phpdocumentor|phpspec|phpunit|sebastian|symfony\/yaml)',
       '^app\/vendor\/[^\/]+\/[^\/]+\/(build|bin|docs?|tests?|changelog|phpunit|upgrade?)',
       'node_modules'
   ];

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $info   = $this->container->info()->get();
        $path   = $this->container->path();
        $vers   = $info['version'];
        $filter = '/'.implode('|', $this->excludes).'/i';

        $this->line(sprintf('Starting: webpack'));

        exec('webpack -p');

        $finder = Finder::create()->files()->in($path)->ignoreVCS(true)->filter(function ($file) use($filter) {
            return !preg_match($filter, $file->getRelativePathname());
        });

        $zip = new \ZipArchive;

        if (!$zip->open($zipFile = "{$path}/pagekit-".$vers.".zip", \ZipArchive::OVERWRITE)) {
            $this->abort("Can't open ZIP extension in '{$zipFile}'");
        }

        foreach ($finder as $file) {
            $zip->addFile($file->getPathname(), $file->getRelativePathname());
        }

        $zip->addFile("{$path}/.bowerrc", '.bowerrc');
        $zip->addFile("{$path}/.htaccess", '.htaccess');

        $zip->addEmptyDir('tmp/');
        $zip->addEmptyDir('tmp/cache');
        $zip->addEmptyDir('tmp/temp');
        $zip->addEmptyDir('tmp/logs');
        $zip->addEmptyDir('tmp/sessions');
        $zip->addEmptyDir('tmp/packages');
        $zip->addEmptyDir('app/database');

        $zip->close();

        $name = basename($zipFile);
        $size = filesize($zipFile) / 1024 / 1024;

        $this->line(sprintf('Building: %s (%.2f MB)', $name, $size));
    }
}
