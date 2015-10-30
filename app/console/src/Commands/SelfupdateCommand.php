<?php

namespace Pagekit\Console\Commands;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\TransferException;
use Pagekit\Application\Console\Command;
use Pagekit\Installer\SelfUpdater;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SelfupdateCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'self-update';

    protected $client;

    /**
     * {@inheritdoc}
     */
    protected $description = 'Checks for newer Pagekit versions and installs the latest.';

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

        try {
            if (!$this->option('url')) {
                $output->write('Requesting Version...');
                $versions = $this->getVersions();
                $output->writeln('<info>done.</info>');

                $output->writeln('');
                $output->writeln('<comment>Latest Version: '.$versions['latest']['version'].'</comment> ');
                $output->writeln('');

                if (!$this->confirm('Update to Version '.$versions['latest']['version'].'? [y/n]')) {
                    return;
                }

                $output->writeln('');

                $url = $versions['latest']['url'];
                $shasum = $versions['latest']['shasum'];
            } else {
                $url = $this->option('url');
                $shasum = $this->option('shasum');
            }

            $tmpFile = tempnam($this->container['path.temp'], 'update_');

            $output->write('Downloading...');
            $this->download($url, $shasum, $tmpFile);
            $output->writeln('<info>done.</info>');

            $updater = new SelfUpdater($output);
            $updater->update($tmpFile);

            $output->write('Migrating...');
            system(sprintf('php %s migrate', $_SERVER['PHP_SELF']));

        } catch (\Exception $e) {

            if (isset($tmpFile) && file_exists($tmpFile)) {
                unlink($tmpFile);
            }

            throw $e;
        }

    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getVersions()
    {
        try {
            $res = $this->client->get($this->container->get('system.api').'/update');
        } catch (\Exception $e) {
            if ($e instanceof TransferException) {
                throw new \RuntimeException('Could not obtain latest Version.');
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
                throw new \RuntimeException('Package url is missing.');
            }

            $data = $this->client->get($url)->getBody();

            if (sha1($data) !== $shasum) {
                throw new \RuntimeException('Package checksum verification failed.');
            }

            if (!file_put_contents($file, $data)) {
                throw new \RuntimeException('Path is not writable.');
            }

        } catch (\Exception $e) {
            if ($e instanceof TransferException) {
                if ($e instanceof BadResponseException) {
                    throw new \RuntimeException('Invalid API key.');
                }
                throw new \RuntimeException('Package download failed.');
            }
            throw $e;
        }
    }
}
