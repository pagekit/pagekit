<?php

namespace Pagekit\Console\Commands;

use Pagekit\Console\Command;
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
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument('extension', InputArgument::OPTIONAL, 'Extension name');
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

        chdir($this->config['path']);

        if (!is_dir($languages)) {
            mkdir($languages, 0777, true);
        }

        $result = [];

        $this->line("Traversing extension files.");

        $progress = new ProgressBar($this->output, count($files));
        $progress->start();

        foreach ($files as $file) {
            $strings = $this->extractStrings($file);
            foreach ($strings as $domain => $messages) {
                if(array_key_exists($domain, $result)) {
                    $result[$domain] = array_merge($result[$domain], $messages);
                } else {
                    $result[$domain] = $messages;
                }
            }
            $progress->advance();
        }

        $progress->finish();
        $this->line("\n");

        // remove strings already present in system "messages"
        if ($extension != 'system') {

            $messages = require($this->getPath('system').'/languages/en_US/messages.php');

            foreach ($result as $domain => $strings) {

                if ('messages' != $domain) {
                    continue;
                }

                foreach (array_keys($result) as $string) {
                    if (isset($messages[$string])) {
                        unset($result[$domain][$string]);
                    }
                }
            }
        }

        $this->writeTranslationFile($result, $extension, $languages);
    }

    /**
     * Extracts translateable strings from a given file.
     *
     * @param  string $file Path to the file
     * @return array Array of strings to be translated, grouped by message domain.
     *               Example:
     *               ['messages' = ['Hello' => 'Hello', 'Apple' => 'Apple'], 'customdomain' => ['One' => 'One']]
     */
    protected function extractStrings($file)
    {
        $content = file_get_contents($file);

        // collect pairs of [$domain, string] from all matches
        $pairs = [];

        // vue matches {{ 'foo' | trans }}
        preg_match_all('/({{\s*(\'|")((?:(?!\2).)+)\2\s*\|\s*trans\s+([^\s]+\s+((\'|")((?:(?!\6).)+)\6))?.*}})/', $content, $matches);
        foreach ($matches[3] as $i => $string) {
            $domain = $matches[7][$i] ?: 'messages';

            $pairs[] = [$domain, $string];
        }

        // vue matches {{ 'foo' | transChoice }}
        preg_match_all('/({{\s*(\'|")((?:(?!\2).)+)\2\s*\|\s*transChoice\s+([^\s]+\s+[^\s]+\s+((\'|")((?:(?!\6).)+)\6))?.*}})/', $content, $matches);
        foreach ($matches[3] as $i => $string) {
            $domain = $matches[7][$i] ?: 'messages';

            $pairs[] = [$domain, $string];
        }

        // php matches ...->trans('foo')
        preg_match_all('/(->trans\((\'|")\s*((?:(?!\2).)+)\2\s*(?:,\s*[^,]+\s*,\s*((\'|")\s*((?:(?!\5).)+)\5).*)?\))/', $content, $matches);
        foreach ($matches[3] as $i => $string) {
            $domain = $matches[6][$i] ?: 'messages';

            $pairs[] = [$domain, $string];
        }

        // php matches ...->transChoice('foo')
        preg_match_all('/(->transChoice\((\'|")\s*((?:(?!\2).)+)\2\s*(?:,\s*[^,]+\s*,\s*[^,]+\s*,\s*((\'|")\s*((?:(?!\5).)+)\5).*)?.+\))/', $content, $matches);
        foreach ($matches[3] as $i => $string) {
            $domain = $matches[6][$i] ?: 'messages';

            $pairs[] = [$domain, $string];
        }

        // sort all pairs according to domain
        $messages = [];
        foreach ($pairs as $pair) {
            list($domain, $string) = $pair;

            if (!isset($messages[$domain])) {
                $messages[$domain] = [];
            }

            $messages[$domain][$string] = $string;
        }

        return $messages;

    }

    /**
     * Returns all files of an extension to extract translations.
     *
     * @param  string $path
     * @return array
     */
    protected function getFiles($path)
    {
        return Finder::create()->files()->in($path)->name('*.{php}');
    }

    /**
     * Returns the extension path.
     *
     * @param  string $path
     * @return array
     */
    protected function getPath($path)
    {

        // TODO: better handling for core modules
        if ($path == 'system') {
            // system module
            $root = $this->config['path'].'/app';
        } else {
            // extensions
            $root = $this->config['path.extensions'];
        }

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

            foreach ($strings as $string) {

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

}