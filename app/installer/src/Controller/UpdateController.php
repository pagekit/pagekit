<?php

namespace Pagekit\Installer\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Pagekit\Application as App;
use Pagekit\Installer\SelfUpdater;

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
                'name' => 'installer:views/update.php'
            ],
            '$data' => [
                'api' => App::get('system.api'),
                'version' => App::version(),
                'channel' => 'stable'
            ]
        ];
    }

    /**
     * @Request({"url": "string", "shasum": "string"}, csrf=true)
     */
    public function downloadAction($url, $shasum)
    {
        try {

            $file = tempnam(App::get('path.temp'), 'update_');
            App::session()->set('system.update', $file);

            $client = new Client;

            $data = $client->get($url)->getBody();

            if (sha1($data) !== $shasum) {
                throw new \RuntimeException('Package checksum verification failed.');
            }

            if (!file_put_contents($file, $data)) {
                throw new \RuntimeException('Path is not writable.');
            }

            return [];

        } catch (\Exception $e) {

            if ($e instanceof TransferException) {
                $error = 'Package download failed.';
            } else {
                $error = $e->getMessage();
            }

            App::abort(500, $error);
        }
    }

    /**
     * @Request(csrf=true)
     */
    public function updateAction()
    {
        if (!$file = App::session()->get('system.update')) {
            App::abort(400, __('You may not call this step directly.'));
        }
        App::session()->remove('system.update');

        return App::response()->stream(function () use ($file) {

            try {

                if (!file_exists($file) || !is_file($file)) {
                    throw new \RuntimeException('File does not exist.');
                }

                $updater = new SelfUpdater();
                $updater->update($file);

            } catch (\Exception $e) {

                http_response_code(400);
                echo $e->getMessage();
            }

        });
    }
}
