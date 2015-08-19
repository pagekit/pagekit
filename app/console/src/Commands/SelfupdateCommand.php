<?php

namespace Pagekit\Console\Commands;

use Pagekit\Console\Command;
use Pagekit\System\Migration\FilesystemLoader;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\TransferException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Question\ConfirmationQuestion;


class SelfupdateCommand extends Command
{

    const API = 'http://pagekit.com/api';

    /**
     * {@inheritdoc}
     */
    protected $name = 'self-update';

    /**
     * {@inheritdoc}
     */
    protected $description = '';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addOption('url', 'u', InputOption::VALUE_REQUIRED, '');
        $this->addOption('shasum', 's', InputOption::VALUE_REQUIRED, '');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->client = new Client;

        if (!$this->option('url')) {
            $output->write('Requesting Version...');
            $versions = $this->getVersions();
            $output->writeln('<info>done.</info>');

            $output->writeln('');
            $output->writeln('<comment>Latest Version: ' . $versions['latest']['version'] . '</comment> ');
            $output->writeln('');

            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('Update to Version ' . $versions['latest']['version'] . '? [y/n]', false);

            if (!$helper->ask($input, $output, $question)) {
                return;
            }
            $output->writeln('');

            $url = $versions['latest']['url'];
            $shasum = $versions['latest']['shasum'];
        } else {
            $url = $this->option('url');
            $shasum = $this->option('shasum');
        }

        $tmpFile = $this->config['path.temp'] . '/' . sha1(uniqid());

        $output->write('Downloading...');
        $this->download($url, $shasum, $tmpFile);
        $output->writeln('<info>done.</info>');

        $output->write('Entering maintenance mode...');
        $this->setMaintenanceMode(true);
        $output->writeln('<info>done.</info>');

        $output->write('Extracting files...');
        $target = $this->config['path'] . '/test';
        $this->extract($tmpFile, $target);
        $output->writeln('<info>done.</info>');

        $output->write('Migrating database...');
        $this->database();
        $output->writeln('<info>done.</info>');

        //TODO: Clear cache

        $output->write('Deactivating maintenance mode...');
        $this->setMaintenanceMode(false);
        $output->writeln('<info>done.</info>');
    }

    /**
     * @return mixed
     */
    protected function getVersions()
    {
        try {
            $res = $this->client->get(self::API . '/update');
        } catch (\Exception $e) {
            if ($e instanceof TransferException) {
                throw new \RuntimeException(__('Could not obtain latest Version.'));
            }
            throw $e;
        }

        return json_decode($res->getBody(), true);
    }

    /**
     * @param $url
     * @param $shasum
     * @param $file
     * @throws \Exception
     */
    public function download($url, $shasum, $file)
    {
        try {
            if (!$url) {
                throw new \RuntimeException(__('Package url is missing.'));
            }

            $data = $this->client->get($url)->getBody();

            if (sha1($data) !== $shasum) {
                throw new \RuntimeException(__('Package checksum verification failed.'));
            }

            if (!file_put_contents($file, $data)) {
                throw new \RuntimeException(__('Path is not writable.'));
            }

        } catch (\Exception $e) {
            unlink($file);

            if ($e instanceof TransferException) {
                if ($e instanceof BadResponseException) {
                    throw new \RuntimeException(__('Invalid API key.'));
                }
                throw new \RuntimeException(__('Package download failed.'));
            }
            throw $e;
        }
    }

    /**
     * @param $file
     * @param $path
     */
    protected function extract($file, $path)
    {
        $zip = new \ZipArchive;
        if ($zip->open($file) === true) {

            $zip->deleteName(".htaccess");

            $zip->extractTo($path);
            $zip->close();
        } else {
            throw new \RuntimeException(__('Package extraction failed.'));
        }

        unlink($file);
    }


    /**
     * Migrating the database.
     */
    protected function database()
    {
        $pagekit = $this->getApplication()->getPagekit();
        $pagekit->extend('migrator', function ($migrator) {
            return $migrator->setLoader(new FilesystemLoader());
        });

        $currentVersion = $pagekit->config('system')->get('version');
        if ($version = $pagekit['migrator']->create('app/system/migrations', $currentVersion)->run()) {
            $pagekit->config('system')->set('version', $version);
        }
    }


    /**
     * Toggles maintenance mode without booting Pagekit application.
     *
     * @param $active
     */
    protected function setMaintenanceMode($active)
    {
        $config = require $this->config['config.file'];

        if ($active) {
            if (!isset($config['system/site'])) {
                $config['system/site'] = [];
            }
            if (!isset($config['system/site']['maintenance'])) {
                $config['system/site']['maintenance'] = [];
            }
            $config['system/site']['maintenance']['enabled'] = true;
        } else {
            unset ($config['system/site']['maintenance']['enabled']);
            if (!count($config['system/site']['maintenance'])) {
                unset($config['system/site']['maintenance']);
            }
            if (!count($config['system/site'])) {
                unset($config['system/site']);
            }
        }

        file_put_contents($this->config['config.file'], '<?php return ' . var_export($config, true) . ';');
    }

}
