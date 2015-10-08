<?php

namespace Pagekit\Console\Commands;

use Pagekit\Application\Console\Command;
use Pagekit\Console\Translate\TransifexApi;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TranslationFetchCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'translation:fetch';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Fetches current translation files from Transifex';

    /**
     * The Transifex Api
     * @var TransifexApi
     */
    protected $api;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument('extension', InputArgument::OPTIONAL, 'Extension name');
        $this->addOption('username', null, InputOption::VALUE_REQUIRED, 'Transifex user name');
        $this->addOption('password', null, InputOption::VALUE_REQUIRED, 'Transifex password');
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $extensions = $this->argument('extension') ? [$this->argument('extension')] : ['system', 'pagekit/blog'];
        $username   = $this->option('username');
        $password   = $this->option('password');

        if (!$username || !$password) {
            throw new \InvalidArgumentException('Credentials missing.');
        }

        $this->api  = new TransifexApi($username, $password, "pagekit-cms");

        foreach ($extensions as $extension) {
            $this->line("Translations for ${extension} ...");
            $this->transifexPull($extension);
        }
    }

    /**
     * Fetches all translations for the specified extension.
     */
    protected function transifexPull($extension)
    {
        $resource = basename($extension);

        foreach ($this->api->fetchLocales($resource) as $locale) {

            $this->line("Fetching for ${locale} ...");
            $translations = $this->api->fetchTranslations($resource, $locale);

            // New languages don't have a folder yet
            $folder = sprintf('%s/languages/%s/', $this->getPath($extension), $locale);
            if (!is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            // Write translation file
            $filename = sprintf('%s/messages.php', $folder);
            $content  = sprintf('<?php return %s;', var_export($translations, true));
            file_put_contents($filename, $content);

        }
    }

    /**
     * Returns the extension path.
     *
     * @param  string $path
     * @return array
     */
    protected function getPath($path)
    {
        $root = $this->container['path.packages'];

        if ($path == "system") {
            $root = $this->container['path'].'/app';
        }

        if (!is_dir($path = "$root/$path")) {
            $this->abort("Can't find extension in '$path'");
        }

        return $path;
    }
}
