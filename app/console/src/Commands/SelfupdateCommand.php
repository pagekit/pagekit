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
            $output->writeln('Latest Version: ' . $versions['latest']['version']);

            $url = $versions['latest']['url'];
            $shasum = $versions['latest']['shasum'];
        } else {
            $url = $this->option('url');
            $shasum = $this->option('shasum');
        }

        $updateDir = $this->config['path.temp'] . '/' . sha1(uniqid());

        $output->write('Downloading...');
        $this->download($url, $shasum, $updateDir);
        $output->writeln('done.');

        $output->writeln('Entering maintenance mode');
        $this->setMaintenanceMode(true);

        $output->writeln('Copying files');
        $this->copy($updateDir);

        $output->writeln('Migrating database');
        $this->database();

        //TODO: Clear cache

        $this->setMaintenanceMode(false);
        $output->writeln('Deactivating maintenance mode');
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
     * @param $updateDir
     * @throws \Exception
     */
    public function download($url, $shasum, $updateDir)
    {
        $file = $updateDir . '/' . uniqid();
        try {
            if (!$url) {
                throw new \RuntimeException(__('Package url is missing.'));
            }

            $data = $this->client->get($url)->getBody();

            if (sha1($data) !== $shasum) {
                throw new \RuntimeException(__('Package checksum verification failed.'));
            }

            if (!mkdir($updateDir) || !file_put_contents($file, $data)) {
                throw new \RuntimeException(__('Path is not writable.'));
            }

            $zip = new \ZipArchive;
            if ($res = $zip->open($file)) {
                $zip->extractTo($updateDir);
                $zip->close();
            } else {
                throw new \RuntimeException(__('Package extraction failed.'));
            }

            unlink($file);

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
     * @param $updateDir
     */
    protected function copy($updateDir)
    {
        unlink("$updateDir/.htaccess");
        $this->moveRecursive($updateDir, $this->config['path'] . '/test');
    }

    /**
     *
     */
    protected function database()
    {
        $pagekit = $this->getApplication()->getPagekit();
        $pagekit->extend('migrator', function ($migrator) {
            return $migrator->setLoader(new FilesystemLoader());
        });

        $currentVersion = $pagekit->config('system')->get('version');
        if ($version = $pagekit['migrator']->create('app/system/migrations', $currentVersion)->run()) {
            $pagekit['config']($this->name)->set('version', $version);
        }
    }

    /**
     * @param $dir
     * @param $path
     */
    protected function moveRecursive($dir, $path)
    {
        foreach (array_diff(scandir($dir), array('..', '.')) as $file) {
            $oldName = $dir . '/' . $file;
            $newName = $path . '/' . $file;

            if (is_dir($oldName)) {
                if (!is_dir($newName)) {
                    mkdir($newName);
                }
                chmod($newName, 0755);
                $this->moveRecursive($oldName, $newName);
                rmdir($oldName);
            } else {
                rename($oldName, $newName);
                chmod($newName, 0755);
            }
        }
    }

}
