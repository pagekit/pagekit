<?php

namespace Pagekit\Installer\Controller;

use GuzzleHttp\Client;
use Pagekit\Application as App;
use Pagekit\Installer\Installer\Verifier;

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
                'api' => App::system()->config('api'),
                'version' => App::version(),
                'channel' => 'stable'
            ]
        ];
    }

    /**
     * @Request({"url": "string", "shasum": "string"})
     */
    public function downloadAction($url, $shasum)
    {
        try {

            $file = tempnam(App::get('path.temp'), 'update_');
            $client = new Client;

            $data = $client->get($url)->getBody();

            if (sha1($data) !== $shasum) {
                throw new \RuntimeException('Package checksum verification failed.');
            }

            if (!file_put_contents($file, $data)) {
                throw new \RuntimeException('Path is not writable.');
            }

            $verifier = new Verifier(require App::get('config.file'));

            $file = basename($file);
            $token = $verifier->hash($file);

            return compact('file', 'token');

        } catch (\Exception $e) {
            if ($e instanceof TransferException) {
                $error = 'Package download failed.';
            } else {
                $error = $e->getMessage();
            }
        }

        App::abort(500, $error);
    }
}
