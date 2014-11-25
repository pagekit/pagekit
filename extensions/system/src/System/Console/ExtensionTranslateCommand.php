<?php

namespace Pagekit\System\Console;

use Pagekit\Component\Translation\Loader\PoFileLoader;
use Pagekit\Framework\Console\Command;
use Pagekit\System\Console\NodeVisitor\NodeVisitor;
use Pagekit\System\Console\NodeVisitor\PhpNodeVisitor;
use Pagekit\System\Console\NodeVisitor\RazrNodeVisitor;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ExtensionTranslateCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'extension:translate';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Generates extension\'s translation .pot/.po/.php files';

    /**
     * The core extensions.
     *
     * @var array
     */
    protected $extensions;

    /**
     * Node visitors.
     *
     * @var NodeVisitor[]
     */
    protected $visitors;

    /**
     * The xgettext command availability.
     *
     * @var bool
     */
    protected $xgettext;

    /**
     * The .po file loader.
     *
     * @var PoFileLoader
     */
    protected $loader;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument('extension', InputArgument::OPTIONAL, 'Extension name');
        $this->addOption('merge', null, InputOption::VALUE_NONE, 'Merge new translations with existing languages');
        $this->addOption('compile', null, InputOption::VALUE_NONE, 'Compile .po files to .php files');
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->extensions = $this->pagekit['config']['extension.core'];
        $this->visitors   = [
            'razr' => new RazrNodeVisitor($this->pagekit['tmpl.razr']),
            'php'  => new PhpNodeVisitor($this->pagekit['tmpl.php'])
        ];
        $this->xgettext   = !defined('PHP_WINDOWS_VERSION_MAJOR') && (bool) exec('which xgettext');
        $this->loader     = new PoFileLoader;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $extension = $this->argument('extension') ?: 'system';
        $files     = $this->getFiles($path = $this->getPath($extension));
        $languages = "$path/languages";

        $this->line("Extracting strings for extension '$extension'");

        chdir($this->pagekit['path']);

        if (!is_dir($languages)) {
            mkdir($languages, 0777, true);
        }

        $result = [];

        foreach ($this->visitors as $name => $visitor) {

            if (!isset($files[$name])) {
                continue;
            }

            $this->line("Traversing extension files: ${name}");

            $progress = new ProgressBar($this->output, count($files[$name]));
            $progress->start();

            foreach ($files[$name] as $file) {
                $visitor->traverse([$file]);
                $progress->advance();
            }

            $result = array_merge_recursive($result, $visitor->results);

            $progress->finish();
            $this->line("\n");
        }

        // remove strings already present in system "messages"
        if ($extension != 'system') {

            $messages = $this->loader->load($this->getPath('system').'/languages/en_US/messages.po', 'en_US');

            foreach ($result as $domain => $strings) {

                if ('messages' != $domain) {
                    continue;
                }

                foreach (array_keys($strings) as $string) {
                    if ($messages->has($string)) {
                        unset($result[$domain][$string]);
                    }
                }
            }
        }

        $this->writeTranslationFile($result, $extension, $languages);
    }

    /**
     * Returns all files of an extension to extract translations.
     *
     * @param  string $path
     * @return array
     */
    protected function getFiles($path)
    {
        $files = [];

        foreach (Finder::create()->files()->in($path)->name('*.{razr,php}') as $file) {

            $file = $file->getPathname();

            foreach ($this->visitors as $name => $visitor) {
                if ($visitor->getEngine()->supports($file)) {
                    $files[$name][] = $file;
                    break;
                }
            }
        }

        return $files;
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
            $this->abort("Can't find extension in '$path'");
        }

        return $path;
    }

    /**
     * Writes the translation file for the given extension.
     *
     * @param array  $messages
     * @param string $extension
     * @param string $path
     */
    protected function writeTranslationFile($messages, $extension, $path)
    {
        foreach ($messages as $domain => $strings) {

            $data = $this->getHeader($extension, $domain);

            foreach ($strings as $string => $args) {

                foreach ($args as $arg) {
                    $file = $this->getRelativePath($arg['file']);
                    $data .= "#: {$file}:{$arg['line']}\n";
                }

                $string = str_replace('"', '\"', $string);
                $data .= "msgid \"".$string."\"\nmsgstr \"\"\n\n";

            }

            file_put_contents($refFile = $path . '/' . $domain . '.pot', $data);

            $files = Finder::create()->files()->in($path)->name("$domain.po");

            if ($this->option('merge') && $this->xgettext) {
                $this->line("Merging new translations with existing languages");
                $progress = new ProgressBar($this->output, $files->count());
                $progress->start();

                foreach ($files as $file) {

                    // merge existing .po file
                    exec('msgmerge --update --no-fuzzy-matching --backup=off ' . $file->getPathname() . ' ' . $refFile. ' 2> /dev/null');

                    $progress->advance();
                }

                $progress->finish();
                $this->line("\n");
            }

            if ($this->option('compile')) {
                $this->line("Compiling .po files to .php files");
                $progress = new ProgressBar($this->output, $files->count());
                $progress->start();

                foreach ($files as $file) {

                    $messages = $this->loader->load($file->getPathname(), 'en', $domain);

                    file_put_contents(preg_replace('/\.po$/', '.php', $file->getPathname()), '<?php return '.var_export($messages->all($domain), true).';');

                    $progress->advance();
                }

                $progress->finish();
            }


            $this->line("\n");
        }
    }

    /**
     * Returns the .pot header.
     *
     * @param  string $extension
     * @param  string $domain
     * @return string
     */
    protected function getHeader($extension, $domain)
    {
        $version = $this->getApplication()->getVersion();
        $date    = date("Y-m-d H:iO");

        return <<<EOD
msgid ""
msgstr ""
"Project-Id-Version: Pagekit $version ($extension, $domain)\\n"
"POT-Creation-Date: $date\\n"
"PO-Revision-Date: YYYY-mm-DD HH:MM+ZZZZ\\n"
"Last-Translator: NAME <EMAIL@ADDRESS>\\n"
"Language-Team: LANGUAGE <LL@li.org>\\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=utf-8\\n"
"Content-Transfer-Encoding: 8bit\\n"


EOD;
    }

    /**
     * Returns the relative path to the root.
     *
     * @param  string $path
     * @return string
     */
    protected function getRelativePath($path)
    {
        $root = $this->pagekit['path'];

        if (0 === strpos($path, $root)) {
            $path = ltrim(str_replace('\\', '/', substr($path, strlen($root))), '/');
        }

        return $path;
    }
}