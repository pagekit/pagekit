<?php

namespace Pagekit\System\Controller;

use GuzzleHttp\Client;
use Pagekit\Component\File\Archive\Zip;
use Pagekit\Component\Package\Downloader\PackageDownloader;
use Pagekit\Component\Package\Exception\ArchiveExtractionException;
use Pagekit\Component\Package\Exception\ChecksumVerificationException;
use Pagekit\Component\Package\Exception\DownloadErrorException;
use Pagekit\Component\Package\Exception\NotWritableException;
use Pagekit\Component\Package\Exception\UnauthorizedDownloadException;
use Pagekit\Component\Package\Loader\JsonLoader;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;

/**
 * @Access("system: manage extensions", admin=true)
 */
class PackageController extends Controller
{
    protected $temp;
    protected $api;
    protected $apiKey;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->temp   = $this['path.temp'];
        $this->api    = $this['config']->get('api.url');
        $this->apiKey = $this['option']->get('system:api.key');
    }

    /**
     * @Request({"type"}, csrf=true)
     * @Response("json")
     */
    public function uploadAction($type = null)
    {
        try {

            $file = $this['request']->files->get('file');

            if ($file === null || !$file->isValid()) {
                throw new Exception(__('No file uploaded.'));
            }

            $package = $this->loadPackage($upload = $file->getPathname());

            if ($type != $package->getType()) {
                throw new Exception(__('Invalid package type.'));
            }

            if ($this->isCore($package->getName())) {
                throw new Exception(__('Core extensions may not be installed.'));
            }

            Zip::extract($upload, "{$this->temp}/".($path = sha1($upload)));

            $extra = $package->getExtra();

            if (isset($extra['image'])) {
                $extra['image'] = $this['url']->to("{$this->temp}/$path/".$extra['image']);
            } else {
                $extra['image'] = $this['url']->to('extension://system/assets/images/placeholder-icon.svg');
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
     * @Response("json")
     */
    public function installAction($package = null, $path = '')
    {
        try {

            if ($package !== null && isset($package['dist'])) {

                $path = sha1(json_encode($package));

                $client = new Client;
                $client->setDefaultOption('query/api_key', $this->apiKey);

                $downloader = new PackageDownloader($client);
                $downloader->downloadFile("{$this->temp}/{$path}", $package['dist']['url'], $package['dist']['shasum']);
            }

            if (!$path) {
                throw new Exception(__('Path not found.'));
            }

            $package = $this->loadPackage($path = "{$this->temp}/{$path}");

            if ($package->getType() == 'theme') {
                $this->installTheme("$path/theme.json", $package);
            } else {
                $this->installExtension("$path/extension.json", $package);
            }

            $this['system']->clearCache();

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

        if (strpos($path, $this->temp) === 0 && file_exists($path)) {
            $this['file']->delete($path);
        }

        return $response;
    }

    protected function installTheme($json, $package)
    {
        $installer = $this['themes']->getInstaller();

        if ($installer->isInstalled($package)) {
            $installer->update($json);
        } else {
            $installer->install($json);
        }
    }

    protected function installExtension($json, $package)
    {
        if ($this->isCore($name = $package->getName())) {
            throw new Exception(__('Core extensions may not be installed.'));
        }

        $installer = $this['extensions']->getInstaller();
        $extension = $this['extensions']->get($name);

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

    protected function isCore($name)
    {
        return in_array($name, $this['config']->get('extension.core', []));
    }
}
