<?php

namespace Pagekit\Console;

use Pagekit\Application\Console\Command;
use Pagekit\Console\NodeVisitor\NodeVisitor;
use Pagekit\Console\NodeVisitor\PhpNodeVisitor;
use Pagekit\Console\NodeVisitor\RazrNodeVisitor;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument('extension', InputArgument::OPTIONAL, 'Extension name');
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->visitors = [
            'php'  => new PhpNodeVisitor($this->container['tmpl.php'])
        ];

        $this->xgettext = !defined('PHP_WINDOWS_VERSION_MAJOR') && (bool) exec('which xgettext');
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

        chdir($this->container['path']);

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

            $messages = require($this->getPath('system').'/languages/en_US/messages.php');

            foreach ($result as $domain => $strings) {

                if ('messages' != $domain) {
                    continue;
                }

                foreach (array_keys($strings) as $string) {
                    if (isset($messages[$string])) {
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
        $root = $this->container['path.extensions'];

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

            $refFile = $path.'/'.$domain.'.pot';
            if (!file_exists($refFile) || !($compare = preg_replace('/^"POT-Creation-Date: (.*)$/im', '', [file_get_contents($refFile), $data]) and $compare[0] === $compare[1])) {
                file_put_contents($refFile, $data);
            }
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
        $root = $this->container['path'];

        if (0 === strpos($path, $root)) {
            $path = ltrim(str_replace('\\', '/', substr($path, strlen($root))), '/');
        }

        return $path;
    }
}
