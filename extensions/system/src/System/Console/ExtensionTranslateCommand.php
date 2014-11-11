<?php

namespace Pagekit\System\Console;

use Pagekit\Component\Translation\Loader\PoFileLoader;
use Pagekit\Framework\Console\Command;
use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Templating\EngineInterface;

class ExtensionTranslateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extension:translate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates translation .pot/.po/.mo files';

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
        $this->visitors = [
            'razr' => new RazrNodeVisitor($this->pagekit['tmpl.razr']),
            'php'  => new PhpNodeVisitor($this->pagekit['tmpl.php'])
        ];
        $this->xgettext = !defined('PHP_WINDOWS_VERSION_MAJOR') && (bool) exec('which xgettext');
        $this->loader = new PoFileLoader();
    }

    /**
     * Execute the console command.
     */
    public function execute(InputInterface $input, OutputInterface $output)
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

            $result = array_merge_recursive($result, $visitor->traverse($files[$name]));
        }

        // remove strings already present in system "messages"
        if ($extension != 'system') {

            $messages = $this->loader->load($this->getPath('system').'/languages/messages.pot', 'en');

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
     * Writes the translation file for the given extension
     *
     */
    protected function writeTranslationFile($messages, $extension, $languages)
    {
        foreach ($messages as $domain => $strings) {

            $data = $this->getHeader($extension, $domain);

            foreach ($strings as $string => $args) {

                foreach ($args as $arg) {
                    $file  = $this->getRelativePath($arg['file']);
                    $data .= "#: {$file}:{$arg['line']}\n";
                }

                $string = str_replace('"', '\"', $string);
                $data .= "msgid \"" . $string . "\"\nmsgstr \"\"\n\n";

            }

            // file_put_contents($refFile = $languages.'/'.$domain.'.pot', str_replace(array_values($files), array_keys($files), $data));
            file_put_contents($refFile = $languages . '/' . $domain . '.pot', $data);

            if (!$this->xgettext) {
                continue;
            }

            foreach (Finder::create()->files()->in($languages)->name("$domain.po") as $file) {

                $this->line("Merging existing .po file " . $file->getFilename());
                exec('msgmerge --update --no-fuzzy-matching --backup=off ' . $file->getPathname() . ' ' . $refFile);

                $this->line("Generating binary .mo file " . preg_replace('/\.po$/', '.mo', $file->getFilename()));
                exec('msgfmt -o  ' . preg_replace('/\.po$/', '.mo', $file->getPathname()) . ' ' . $file->getPathname());
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
        $date = date("Y-m-d H:iO");

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

abstract class NodeVisitor
{
    /**
     * @var string
     */
    public $file;

    /**
     * @var array
     */
    public $results = [];

    /**
     * @var EngineInterface
     */
    public $engine;

    public function __construct(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    /**
     * @return EngineInterface
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * Starts traversing an array of files.
     *
     * @param  array $files
     * @return array
     */
    abstract public function traverse(array $files);

    /**
     * @param  string $name
     * @return string
     */
    protected function loadTemplate($name)
    {
        return $this->file = $name;
    }
}

class PhpNodeVisitor extends NodeVisitor implements \PhpParser\NodeVisitor
{
    /**
     * {@inheritdoc}
     */
    public function traverse(array $files)
    {
        $parser = new Parser(new Lexer());
        $traverser = new NodeTraverser();
        $traverser->addVisitor($this);

        foreach ($files as $file) {

            try {

                $traverser->traverse($parser->parse(file_get_contents($this->loadTemplate($file))));

            } catch (\Exception $e) {}
        }

        return $this->results;
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Expr\FuncCall
            && ($node->name->parts[0] == '__' || $node->name->parts[0] == '_c')
            && isset($node->args[0]) && is_string($string = $node->args[0]->value->value))
        {
            $key = $node->name->parts[0] == '__' ? 2 : 3;
            $domain = isset($node->args[$key]) && is_string($node->args[$key]->value->value) ? $node->args[$key]->value->value : 'messages';
            $this->results[$domain][$string][] = ['file' => $this->file, 'line' => $node->getLine()];
        }
    }

    public function beforeTraverse(array $nodes) {}
    public function leaveNode(Node $node) {}
    public function afterTraverse(array $nodes) {}
}

class RazrNodeVisitor extends PhpNodeVisitor implements \PhpParser\NodeVisitor
{
    /**
     * {@inheritdoc}
     */
    public function loadTemplate($name)
    {
        $this->file = $name;

        return $this->engine->loadTemplate($name);
    }
}
