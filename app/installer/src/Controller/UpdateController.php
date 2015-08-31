<?php

namespace Pagekit\Installer\Controller;

use GuzzleHttp\Client;
use Pagekit\Application as App;
use Pagekit\Installer\Updater;

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

            return [
                'file' => basename($file),
                'token' => self::hash($shasum, App::system()->config('key'))
            ];

        } catch (\Exception $e) {

            if ($e instanceof TransferException) {
                $error = 'Package download failed.';
            } else {
                $error = $e->getMessage();
            }
        }

        App::abort(500, $error);
    }

    /**
     * @Request({"file": "string", "token": "string"}, csrf=true)
     */
    public function updateAction($file, $token)
    {
        return App::response()->stream(function () use ($file, $token) {

            try {

                $file = preg_replace('/[^a-z0-9_\-\.]/i', '', $file);
                $file = App::get('path.temp') . '/' . $file;

                if (!file_exists($file) || !is_file($file)) {
                    throw new \RuntimeException('File does not exist.');
                }

                if (isset($request['token']) && !self::verify(sha1_file($file), $token)) {
                    throw new \RuntimeException('File token verification failed.');
                }

                $updater = new Updater();
                $updater->update($file);

            } catch (\Exception $e) {

                http_response_code(400);
                echo $e->getMessage();
            }

        });
    }

    /**
     * Calculates HMAC-SHA1 for given data.
     *
     * @param  string $data
     * @param  string $key
     * @return string
     */
    public static function hash($data, $key)
    {
        return base64_encode(extension_loaded('hash') ?
            hash_hmac('sha1', $data, $key, true) : pack('H*', sha1(
                (str_pad($key, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) .
                pack('H*', sha1((str_pad($key, 64, chr(0x00)) ^
                        (str_repeat(chr(0x36), 64))) . $data)))));
    }

    /**
     * Verifies integrity of a given data.
     *
     * @param  string $data
     * @param  string $token
     * @return bool
     */
    public static function verify($data, $token)
    {
        return sha1(self::hash($data)) === sha1($token);
    }
}
