<?php

namespace Pagekit\System\Controller;

use GuzzleHttp\Client;
use Pagekit\Application as App;
use Pagekit\Framework\Controller\Exception;
use Pagekit\Package\Downloader\PackageDownloader;
use Pagekit\Package\Exception\ArchiveExtractionException;
use Pagekit\Package\Exception\ChecksumVerificationException;
use Pagekit\Package\Exception\DownloadErrorException;
use Pagekit\Package\Exception\NotWritableException;
use Pagekit\Package\Exception\UnauthorizedDownloadException;

/**
 * @Access("system: software updates", admin=true)
 */
class UpdateController
{
    /**
     * @Response("extensions/system/views/admin/settings/update.razr")
     */
    public function indexAction()
    {
        return ['head.title' => __('Update'), 'api' => App::config()->get('api.url'), 'channel' => App::option()->get('system:app.release_channel', 'stable'), 'version' => App::config()->get('app.version')];
    }

    /**
     * @Request({"update": "json"})
     * @Response("json")
     */
    public function downloadAction($update = null)
    {
        try {

            if ($update) {
                App::session()->set('system.update', $update);
            } else {
                throw new Exception(__('Unable to find update.'));
            }

            App::session()->set('system.updateDir', $path = App::get('path.temp').'/'.sha1(uniqid()));

            $client = new Client;
            $client->setDefaultOption('query/api_key', App::option()->get('system:api.key'));

            $downloader = new PackageDownloader($client);
            $downloader->downloadFile($path, $update['url'], $update['shasum']);

            $response = ['message' => __('Copying files...'), 'step' => App::url()->route('@system/update/copy'), 'progress' => 33];

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

            if (!$update = App::session()->get('system.update') or !$updateDir = App::session()->get('system.updateDir')) {
                throw new Exception(__('You may not call this step directly.'));
            }

            // TODO: create maintenance file
            // TODO: cleanup old files

            App::file()->delete("$updateDir/.htaccess");
            App::file()->copyDir($updateDir, App::path());
            App::file()->delete($updateDir);
            App::system()->clearCache();
            App::session()->remove('system.updateDir');

            $response = ['message' => __('Updating database...'), 'step' => App::url()->route('@system/update/database'), 'progress' => 66];

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

            if (!$update = App::session()->get('system.update')) {
                throw new Exception(__('You may not call this step directly.'));
            }

            App::system()->enable();
            App::system()->clearCache();
            App::session()->remove('system.update');

            $response = ['message' => __('Installed successfully.'), 'redirect' => App::url()->route('@system/admin'), 'progress' => 100];

        } catch (\Exception $e) {

            $response = ['error' => $e->getMessage()];
        }

        return $response;
    }
}
