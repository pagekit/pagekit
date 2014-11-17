<?php

namespace Pagekit\System\Console;

use Pagekit\Framework\Console\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class BuildCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds a release';

    /**
     * Path filters to exclude from the build.
     *
     * @var string[]
     */
    protected $excludes = [
        '^(app\/cache|app\/database|app\/logs|app\/sessions|app\/temp|storage|config\.php|pagekit.+\.zip)',
        '^extensions\/(?!(installer|page|blog|system)\/).*',
        '^extensions\/.+\/languages\/.+\.(po|pot)',
        '^themes\/(?!(alpha)\/).*',
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
     * Builds a .zip release file.
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $vers = $this->getApplication()->getVersion();
        $path = $this->pagekit['path'];
        $file = $this->option('output') ?: $path;
        $dev  = preg_replace_callback('/(\d)$/', function ($matches){
                return $matches[1] + 1;
            }, $vers).'-dev'.time(true);

        // compile translation files
        $cmd = $this->getApplication()->get('translation:compile');
        foreach (['system', 'page', 'blog', 'installer'] as $extension) {
            $cmd->run(new ArrayInput(compact('extension')), $output);
        }

        $zip = new \ZipArchive;

        if (!$zip->open($zipFile = "{$file}/pagekit-".($this->option('development') ? $dev : $vers).'.zip', \ZipArchive::OVERWRITE)) {
            $this->error("Can't open ZIP extension in '$zipFile'");
            exit;
        }

        $finder = Finder::create()
            ->files()
            ->in($path)
            ->ignoreVCS(true)
            ->filter(function ($file) {
                return !preg_match('/'.implode('|', $this->excludes).'/i', $file->getRelativePathname());
            });

        foreach ($finder as $file) {
            $zip->addFile($file->getPathname(), $file->getRelativePathname());
        }

        $zip->addEmptyDir('app/cache');
        $zip->addEmptyDir('app/database');
        $zip->addEmptyDir('app/logs');
        $zip->addEmptyDir('app/sessions');
        $zip->addEmptyDir('app/temp');
        $zip->addEmptyDir('storage');
        $zip->addFile($path.'/.htaccess', '.htaccess');
        $zip->addFile($path.'/app/database/.htaccess', 'app/database/.htaccess');

        if ($this->option('development')) {
            $zip->addFromString('app/config/app.php', str_replace("'version' => '{$vers}',", "'version' => '{$dev}',", file_get_contents("{$path}/app/config/app.php")));
        }

        $zip->close();

        $name = basename($zipFile);
        $size = filesize($zipFile) / 1024 / 1024;

        $this->line(sprintf('Building: %s (%.2f MB)', $name, $size));
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addOption('development', 'd', InputOption::VALUE_NONE, 'Development Build')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Output Path');
    }
}
