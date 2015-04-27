<?php

namespace Pagekit\System\Controller;

use GuzzleHttp\Client;
use Pagekit\Application as App;
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
    public function indexAction()
    {
        return [
            '$view' => [
                'title' => __('Update'),
                'name'  => 'system:views/admin/settings/update.php'
            ],
            '$config' => [
                'api' => App::system()->config('api.url'),
                'channel' => App::system()->config('release_channel', 'stable'),
                'version' => App::version()
            ]
        ];
    }

    /**
     * @Request({"update": "array"})
     */
    public function downloadAction($update = null)
    {
        try {

            if ($update) {
                App::session()->set('system.update', $update);
            } else {
                App::abort(400, __('Unable to find update.'));
            }

            App::session()->set('system.updateDir', $path = App::get('path.temp').'/'.sha1(uniqid()));

            $client = new Client;
            $client->setDefaultOption('query/api_key', App::system()->config('api.key'));

            $downloader = new PackageDownloader($client);
            $downloader->downloadFile($path, $update['url'], $update['shasum']);

            return 'success';

        } catch (ArchiveExtractionException $e) {
            $error = __('Package extraction failed.');
        } catch (ChecksumVerificationException $e) {
            $error = __('Package checksum verification failed.');
        } catch (UnauthorizedDownloadException $e) {
            $error = __('Invalid API key.');
        } catch (DownloadErrorException $e) {
            $error = __('Package download failed.');
        } catch (NotWritableException $e) {
            $error = __('Path is not writable.');
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return compact('error');
    }

    public function copyAction()
    {
        if (!$update = App::session()->get('system.update') or !$updateDir = App::session()->get('system.updateDir')) {
            App::abort(400, __('You may not call this step directly.'));
        }

        // TODO: create maintenance file
        // TODO: cleanup old files

        App::file()->delete("$updateDir/.htaccess");
        App::file()->copyDir($updateDir, App::path());
        App::file()->delete($updateDir);
        App::module('system/cache')->clearCache();
        App::session()->remove('system.updateDir');

        return 'success';
    }

    public function databaseAction()
    {
        if (!$update = App::session()->get('system.update')) {
            App::abort(400, __('You may not call this step directly.'));
        }

        App::system()->enable();
        App::module('system/cache')->clearCache();
        App::session()->remove('system.update');

        return 'success';
    }
}
