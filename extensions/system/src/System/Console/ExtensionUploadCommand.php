<?php

namespace Pagekit\System\Console;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Post\PostFile;
use Pagekit\Component\Package\Loader\JsonLoader;
use Pagekit\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ExtensionUploadCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extension:upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uploads an extension to the marketplace';

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->upload($this->argument('extension'), $this->pagekit['path.extensions'], 'extension.json');
    }

    /**
     * Creates and uploads a .zip release file.
     *
     * @param string $name
     * @param string $path
     * @param string $json
     */
    protected function upload($name, $path, $json)
    {
        $temp = $this->pagekit['path'].'/app/temp';
        $api  = $this->pagekit['config']['api.url'];

        if (!is_dir($path = "$path/$name")) {
            $this->error("Can't find $json in '$path'");
            exit;
        }

        if (!$key = $this->pagekit['option']->get('system:api.key')) {
            $this->error("Please set your api key");
            exit;
        }

        $loader  = new JsonLoader;
        $package = $loader->load("$path/$json");
        $version = $package->getVersion();

        $zip = new \ZipArchive;
        $zip->open($zipFile = "$temp/$name-$version.zip", \ZipArchive::OVERWRITE);

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

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument('extension', InputArgument::REQUIRED, 'Extension name')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force overwrite');
    }
}
