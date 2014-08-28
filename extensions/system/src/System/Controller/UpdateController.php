<?php

namespace Pagekit\System\Controller;

use GuzzleHttp\Client;
use Pagekit\Component\Package\Downloader\PackageDownloader;
use Pagekit\Component\Package\Exception\ArchiveExtractionException;
use Pagekit\Component\Package\Exception\ChecksumVerificationException;
use Pagekit\Component\Package\Exception\DownloadErrorException;
use Pagekit\Component\Package\Exception\NotWritableException;
use Pagekit\Component\Package\Exception\UnauthorizedDownloadException;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;

/**
 * @Access("system: software updates", admin=true)
 */
class UpdateController extends Controller
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
     * @Response("extension://system/views/admin/settings/update.razr")
     */
    public function indexAction()
    {
        return ['head.title' => __('Update'), 'api' => $this->api, 'channel' => $this['option']->get('system:app.release_channel', 'stable'), 'version' => $this['config']->get('app.version')];
    }

    /**
     * @Request({"update": "json"})
     * @Response("json")
     */
    public function downloadAction($update = null)
    {
        try {

            if ($update) {
                $this['session']->set('system.update', $update);
            } else {
                throw new Exception(__('Unable to find update.'));
            }

            $this['session']->set('system.updateDir', $path = $this->temp.'/'.sha1(uniqid()));

            $client = new Client;
            $client->setDefaultOption('query/api_key', $this->apiKey);

            $downloader = new PackageDownloader($client);
            $downloader->downloadFile($path, $update['url'], $update['shasum']);

            $response = ['message' => __('Copying files...'), 'step' => $this['url']->route('@system/update/copy'), 'progress' => 33];

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

        return $response;
    }

    /**
     * @Response("json")
     */
    public function copyAction()
    {
        try {

            if (!$update = $this['session']->get('system.update') or !$updateDir = $this['session']->get('system.updateDir')) {
                throw new Exception(__('You may not call this step directly.'));
            }

            // TODO: create maintenance file
            // TODO: cleanup old files

            $this['file']->delete("$updateDir/.htaccess");
            $this['file']->copyDir($updateDir, $this['path']);
            $this['file']->delete($updateDir);
            $this['system']->clearCache();
            $this['session']->remove('system.updateDir');

            $response = ['message' => __('Updating database...'), 'step' => $this['url']->route('@system/update/database'), 'progress' => 66];

        } catch (\Exception $e) {

            $response = ['error' => $e->getMessage()];
        }

        return $response;
    }

    /**
     * @Response("json")
     */
    public function databaseAction()
    {
        try {

            if (!$update = $this['session']->get('system.update')) {
                throw new Exception(__('You may not call this step directly.'));
            }

            $this['system']->enable();
            $this['system']->clearCache();
            $this['session']->remove('system.update');

            $response = ['message' => __('Installed successfully.'), 'redirect' => $this['url']->route('@system/system/admin'), 'progress' => 100];

        } catch (\Exception $e) {

            $response = ['error' => $e->getMessage()];
        }

        return $response;
    }
}
