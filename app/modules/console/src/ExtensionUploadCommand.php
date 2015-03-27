<?php

namespace Pagekit\Console;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Post\PostFile;
use Pagekit\Application\Console\Command;
use Pagekit\Package\Loader\JsonLoader;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ExtensionUploadCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'extension:upload';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Uploads an extension to the marketplace';

    /**
     * @var string
     */
    protected $package;

    /**
     * @var string
     */
    protected $json = 'extension.json';

    /**
     * @var string
     */
    protected $path;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Package name');
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force overwrite');
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->package = $this->argument('name');
        $this->path    = $this->container['path.extensions'];
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $temp = $this->container['path'].'/app/temp';
        $api  = $this->container['system']->config('api.url');

        if (!is_dir($path = "{$this->path}/{$this->package}")) {
            $this->abort("Can't find {$this->json} in '{$this->path}'");
        }

        if (!$key = $this->container['system']->config('api.key')) {
            $this->abort("Please set your api key");
        }

        $loader  = new JsonLoader;

        $package = $loader->load("{$path}/{$this->json}");
        $version = $package->getVersion();

        $zip = new \ZipArchive;
        $zip->open($zipFile = "{$temp}/{$this->package}-{$version}.zip", \ZipArchive::OVERWRITE);

        $finder = new Finder;
        $finder->files()->in($path)->ignoreVCS(true);

        foreach ($finder as $file) {
            $zip->addFile($file->getPathname(), $file->getRelativePathname());
        }

        $zip->close();

        $time = microtime(true);
        $name = basename($zipFile);
        $size = filesize($zipFile) / 1024 / 1024;

        $this->line(sprintf('Uploading: %s (%.2f MB) ...', $name, $size));

        try {

            $client = new Client;
            $client->post("$api/package/upload", [
                'body' => [
                    'api_key' => $key,
                    'force'   => $this->option('force'),
                    'file'    => new PostFile('file', fopen($zipFile, 'r'))
                ]
            ]);

            $this->line(sprintf('Finished (%d KB/s)', $size * 1024 / (microtime(true) - $time)));

        } catch (BadResponseException $e) {

            $data = json_decode($e->getResponse()->getBody(true), true);
            $this->line(sprintf('Error: %s', $data['error']));

        }
    }
}
