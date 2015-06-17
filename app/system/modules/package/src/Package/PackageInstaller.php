<?php

namespace Pagekit\System\Package;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\TransferException;
use Pagekit\Filesystem\Archive\Zip;
use Pagekit\Filesystem\Filesystem;

class PackageInstaller
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Filesystem
     */
    protected $file;

    /**
     * Constructor.
     *
     * @param Filesystem      $file
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client = null, Filesystem $file = null)
    {
        $this->client = $client ?: new Client;
        $this->file = $file ?: new Filesystem;
    }

    /**
     * Installs a package.
     *
     * @param  PackageInterface $package
     * @param  string           $path
     * @return bool
     * @throws \RuntimeException
     */
    public function install(PackageInterface $package, $path)
    {
        $name = $package->getName();

        if (!$source = $package->get('path')) {
            throw new \RuntimeException(__('Package path is missing.'));
        }

        if (file_exists("$path/$name")) {
            $this->file->delete("$path/$name");
        }

        return $this->file->copyDir($source, "$path/$name");
    }

    /**
     * Uninstalls a package.
     *
     * @param  PackageInterface $package
     * @return bool
     * @throws \RuntimeException
     */
    public function uninstall(PackageInterface $package)
    {
        if (!$path = $package->get('path')) {
            throw new \RuntimeException(__('Package path is missing.'));
        }

        return $this->file->delete($path);
    }

    /**
     * Download and extract a package.
     *
     * @param  PackageInterface $package
     * @param  string           $path
     * @throws \Exception
     * @throws \RuntimeException
     */
    public function download(PackageInterface $package, $path)
    {
        $file = $path.'/'.uniqid();

        try {

            if (!$url = $package->get('dist.url')) {
                throw new \RuntimeException(__('Package url is missing.'));
            }

            $data = $this->client->get($url)->getBody();

            if ($shasum = $package->get('dist.shasum') and sha1($data) !== $shasum) {
                throw new \RuntimeException(__('Package checksum verification failed.'));
            }

            if (!$this->file->makeDir($path) || !file_put_contents($file, $data)) {
                throw new \RuntimeException(__('Path is not writable.'));
            }

            if (Zip::extract($file, $path) !== true) {
                throw new \RuntimeException(__('Package extraction failed.'));
            }

            $this->file->delete($file);

        } catch (\Exception $e) {

            $this->file->delete($path);

            if ($e instanceof TransferException) {

                if ($e instanceof BadResponseException) {
                    throw new \RuntimeException(__('Invalid API key.'));
                }

                throw new \RuntimeException(__('Package download failed.'));
            }

            throw $e;
        }
    }
}
