<?php

namespace Pagekit\Console\Commands;

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

    /**
     * {@inheritdoc}
     */
    protected $description = 'Checks for newer Pagekit versions and installs the latest';

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

        try {
            if (!$this->option('url')) {
                $output->write('Requesting Version...');
                $versions = $this->getVersions();
                $output->writeln('<info>done.</info>');

                $output->writeln('');
                $output->writeln('<comment>Latest Version: ' . $versions['latest']['version'] . '</comment> ');
                $output->writeln('');

                if (!$this->confirm('Update to Version ' . $versions['latest']['version'] . '? [y/n]')) {
                    return;
                }

                $output->writeln('');

                $url = $versions['latest']['url'];
            } else {
                $url = $this->option('url');
            }

            $tmpFile = tempnam($this->container['path.temp'], 'update_');

            $output->write('Downloading...');
            $this->download($url, $tmpFile);
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
        if (!($res = file_get_contents($this->container->get('system.api') . '/api/update'))) {
            App::abort(500, 'Could not obtain latest Version.');
        }

        return json_decode($res, true);
    }

    /**
     * @param $url
     * @param $file
     * @throws \Exception
     */
    public function download($url, $file)
    {
        if (!$url) {
            throw new \RuntimeException('Package url is missing.');
        }

        if (!file_put_contents($file, @fopen($url, 'r'))) {
            App::abort(500, 'Download failed or Path not writable.');
        }
    }
}
