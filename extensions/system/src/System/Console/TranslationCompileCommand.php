<?php

namespace Pagekit\System\Console;

use Pagekit\Framework\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class TranslationCompileCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translation:compile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compiles translation files from .po -> .mo format';

    /**
     * The core extensions.
     *
     * @var array
     */
    protected $extensions;

    /**
     * The xgettext command availability.
     *
     * @var bool
     */
    protected $xgettext;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->addArgument('extension', InputArgument::OPTIONAL, 'Extension name');
    }

    /**
     * Initialize the console command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->extensions = $this->pagekit['config']['extension.core'];
        $this->xgettext   = !defined('PHP_WINDOWS_VERSION_MAJOR') && (bool)exec('which xgettext');
    }

    /**
     * Execute the console command.
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $extension = $this->argument('extension') ?: 'system';
        $path      = $this->getPath($extension);
        $languages = "$path/languages";

        if (!$this->xgettext) {
            $this->error("Can't compile language files for extension '${extension}'. Please install xgettext on your system.");
            return;
        }

        $this->line("Compiling language files for extension '$extension'");

        chdir($this->pagekit['path']);

        if (!is_dir($languages)) {
            mkdir($languages, 0777, true);
        }

        $files = Finder::create()->files()->in($languages)->name("*.po");

        $progress = new ProgressBar($output, $files->count());
        $progress->start();

        foreach ($files as $file) {

            exec('msgfmt -o  '.preg_replace('/\.po$/', '.mo', $file->getPathname()).' '.$file->getPathname());
            $progress->advance();

        }

        $progress->finish();
        $this->line("\n");

    }

    /**
     * Returns the extension path.
     *
     * @param  string $path
     * @return array
     */
    protected function getPath($path)
    {
        $root = $this->pagekit['path.extensions'];

        if (!is_dir($path = "$root/$path")) {
            $this->error("Can't find extension in '$path'");
            exit;
        }

        return $path;
    }
}
