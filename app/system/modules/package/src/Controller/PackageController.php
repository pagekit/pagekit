<?php

namespace Pagekit\System\Controller;

use GuzzleHttp\Client;
use Pagekit\Application as App;
use Pagekit\Application\Exception;
use Pagekit\Filesystem\Archive\Zip;
use Pagekit\Package\Downloader\PackageDownloader;
use Pagekit\Package\Exception\ArchiveExtractionException;
use Pagekit\Package\Exception\ChecksumVerificationException;
use Pagekit\Package\Exception\DownloadErrorException;
use Pagekit\Package\Exception\NotWritableException;
use Pagekit\Package\Exception\UnauthorizedDownloadException;
use Pagekit\Package\Loader\JsonLoader;

/**
 * @Access("system: manage extensions", admin=true)
 */
class PackageController
{
    /**
     * @Request({"type"}, csrf=true)
     */
    public function uploadAction($type = null)
    {
        try {

            $temp = App::get('path.temp');
            $file = App::request()->files->get('file');

            if ($file === null || !$file->isValid()) {
                throw new Exception(__('No file uploaded.'));
            }

            $package = $this->loadPackage($upload = $file->getPathname());

            if ($type != $package->getType()) {
                throw new Exception(__('Invalid package type.'));
            }

            Zip::extract($upload, "{$temp}/".($path = sha1($upload)));

            $extra = $package->getExtra();

            if (isset($extra['image'])) {
                $extra['image'] = App::url("{$temp}/$path/".$extra['image']);
            } else {
                $extra['image'] = App::url('app/system/assets/images/placeholder-icon.svg');
            }

            $response = [
                'package' => [
                    'name' => $package->getName(),
                    'type' => $package->getType(),
                    'title' => $package->getTitle(),
                    'description' => $package->getDescription(),
                    'version' => $package->getVersion(),
                    'author' => $package->getAuthor(),
                    'shasum' => sha1_file($upload),
                    'extra' => $extra
                ],
                'install' => $path
            ];

        } catch (Exception $e) {
            $response = ['error' => $e->getMessage()];
        }

        return $response;
    }

    /**
     * @Request({"package": "json", "path": "alnum"}, csrf=true)
     */
    public function installAction($package = null, $path = '')
    {
        try {

            $temp = App::get('path.temp');

            if ($package !== null && isset($package['dist'])) {

                $path = sha1(json_encode($package));

                $client = new Client;
                $client->setDefaultOption('query/api_key', App::system()->config('api.key'));

                $downloader = new PackageDownloader($client);
                $downloader->downloadFile("{$temp}/{$path}", $package['dist']['url'], $package['dist']['shasum']);
            }

            if (!$path) {
                throw new Exception(__('Path not found.'));
            }

            $package = $this->loadPackage($path = "{$temp}/{$path}");

            if ($package->getType() == 'theme') {
                $this->installTheme("$path/theme.json", $package);
            } else {
                $this->installExtension("$path/extension.json", $package);
            }

            App::module('system/cache')->clearCache();

            $response = ['message' => __('Package "%name%" installed.', ['%name%' => $package->getName()])];

        } catch (ArchiveExtractionException $e) {
            $response = ['error' => __('Package extraction failed.')];
        } catch (ChecksumVerificationException $e) {
            $response = ['error' => __('Package checksum verification failed.')];
        } catch (UnauthorizedDownloadException $e) {
            $response = ['error' => __('Invalid API key.')];
        } catch (DownloadErrorException $e) {
            $response = ['error' => __('Package download failed.')];
        } catch (NotWritableException $e) {
            $response = ['error' => __('Path is not writable.')];
        } catch (Exception $e) {
            $response = ['error' => $e->getMessage()];
        }

        if (strpos($path, $temp) === 0 && file_exists($path)) {
            App::file()->delete($path);
        }

        return $response;
    }

    protected function installTheme($json, $package)
    {
        $installer = App::package()->getInstaller('theme');

        if ($installer->isInstalled($package)) {
            $installer->update($json);
        } else {
            $installer->install($json);
        }
    }

    protected function installExtension($json, $package)
    {
        $name = $package->getName();

        $installer = App::package()->getInstaller('extension');
        $extension = App::module($name);

        if ($installer->isInstalled($package)) {

            if (isset($extension)) {
               $extension->disable();
            }

            $installer->update($json);

        } else {
            $installer->install($json);
        }
    }

    protected function loadPackage($file)
    {
        try {

            if (is_dir($file)) {

                $json = realpath("$file/theme.json") ?: realpath("$file/extension.json");

            } elseif (is_file($file)) {

                $zip = new \ZipArchive;

                if ($zip->open($file) === true) {
                    $json = $zip->getFromName("theme.json") ?: $zip->getFromName("extension.json");
                    $zip->close();
                }
            }

            if (isset($json) && $json) {

                $loader  = new JsonLoader;
                $package = $loader->load($json);

                return $package;
            }

            throw new Exception;

        } catch (\Exception $e) {
            throw new Exception(__('Can\'t load json file from package.'));
        }
    }
}
