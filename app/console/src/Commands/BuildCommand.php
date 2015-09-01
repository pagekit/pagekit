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
       '^(app\/database|packages|storage|tmp|config\.php|pagekit.+\.zip)',
       '^vendor\/assets\/[^\/]+\/(dist\/vue-.+\.js|dist\/jquery\.js|lodash\.js)',
       '^vendor\/assets\/(jquery|vue)\/(src|perf)',
       '^vendor\/lusitanian\/oauth\/examples',
       '^vendor\/maximebf\/debugbar\/src\/DebugBar\/Resources',
       '^vendor\/nickic\/php-parser\/(grammar|test_old)',
       '^vendor\/(phpdocumentor|phpspec|phpunit|sebastian|symfony\/yaml)',
       '^vendor\/[^\/]+\/[^\/]+\/(build|bin|docs?|tests?|changelog|phpunit|upgrade?)',
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

        system('webpack -p');

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
        $zip->addEmptyDir('storage');

        $zip->close();

        $name = basename($zipFile);
        $size = filesize($zipFile) / 1024 / 1024;

        $this->line(sprintf('Building: %s (%.2f MB)', $name, $size));
    }
}
