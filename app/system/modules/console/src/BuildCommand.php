<?php

namespace Pagekit\Console;

use Pagekit\Application\Console\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

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
        '^(tmp|app\/database|storage|config\.php|pagekit.+\.zip)',
        '^extensions\/(?!(installer|page|blog|system)\/).*',
        '^extensions\/.+\/languages\/.+\.(po|pot)',
        '^themes\/(?!(alpha)\/).*',
        '^vendor\/assets\/(jquery|vue)\/(src|dist\/jquery.js)',
        '^vendor\/doctrine\/(annotations|cache|collections|common|dbal|inflector|lexer)\/(bin|docs|tests|build|phpunit|run|upgrade|composer\.lock)',
        '^vendor\/guzzlehttp\/(guzzle|streams)\/(docs|tests|makefile|phpunit)',
        '^vendor\/ircmaxell\/.+\/(test|phpunit|version-test|composer\.lock)',
        '^vendor\/lusitanian\/oauth\/(examples|tests)',
        '^vendor\/nikic\/php-parser\/(bin|doc|grammar|test|test_old|phpunit)',
        '^vendor\/pagekit\/.+\/(tests\/|phpunit)',
        '^vendor\/pimple\/pimple\/(tests|phpunit)',
        '^vendor\/psr\/.+\/(test\/|phpunit)',
        '^vendor\/swiftmailer\/swiftmailer\/(doc|notes|tests|test-suite|build|phpunit)',
        '^vendor\/symfony\/.+\/(tests\/|phpunit)',
        '\/node_modules'
    ];

    /**
     * @var string
     */
    protected $filter;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addOption('development', 'd', InputOption::VALUE_NONE, 'Development Build');
        $this->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Output path');
        $this->filter = '/'.implode('|', $this->excludes).'/i';
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $vers = $this->getApplication()->getVersion();
        $root = $this->container['path'];
        $path = $this->option('output') ?: $root;
        $dev  = preg_replace_callback('/(\d+)$/', function ($matches) { return $matches[1] + 1; }, $vers).'-dev';

        // compile translation files
        try {

            // TODO system is no longer an extension
            $cmd = $this->getApplication()->get('extension:translate');
            foreach (['system', 'blog'] as $extension) {
                $cmd->run(new ArrayInput(['extension' => $extension, '--compile' => true]), $output);
            }

        } catch (\InvalidArgumentException $e) {
            $this->info("Could not compile language files. Command does not exist.");
        }

        $zip = new \ZipArchive;

        if (!$zip->open($zipFile = "{$path}/pagekit-".($this->option('development') ? $dev : $vers).".zip", \ZipArchive::OVERWRITE)) {
            $this->abort("Can't open ZIP extension in '$zipFile'");
        }

        $finder = Finder::create()
            ->files()
            ->in($root)
            ->ignoreVCS(true)
            ->filter(function ($file) {
                return !preg_match($this->filter, $file->getRelativePathname());
            });

        foreach ($finder as $file) {
            $zip->addFile($file->getPathname(), $file->getRelativePathname());
        }

        $zip->addEmptyDir('tmp');
        $zip->addEmptyDir('tmp/cache');
        $zip->addEmptyDir('tmp/logs');
        $zip->addEmptyDir('tmp/sessions');
        $zip->addEmptyDir('app/database');
        $zip->addEmptyDir('storage');
        $zip->addFile($root.'/.htaccess', '.htaccess');
        $zip->addFile($root.'/app/database/.htaccess', 'app/database/.htaccess');

        if ($this->option('development')) {
            $zip->addFromString('app/config/app.php', str_replace("'version' => '{$vers}',", "'version' => '{$dev}',", file_get_contents("{$root}/app/config/app.php")));
        }

        $zip->close();

        $name = basename($zipFile);
        $size = filesize($zipFile) / 1024 / 1024;

        $this->line(sprintf('Building: %s (%.2f MB)', $name, $size));
    }
}
