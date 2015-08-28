<?php

namespace Pagekit\Installer\Controller;

use Pagekit\Application as App;
use Pagekit\Console\UriVerifier;

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
     * @Request({"update": "array"})
     */
    public function runAction($update = null)
    {
        try {

            $params = [
                'command' => 'self-update',
                '--url' => $update['url'],
                '--shasum' => $update['shasum']
            ];

            $verifier = new UriVerifier(require App::get('config.file'));

            return App::redirect(
                $verifier->sign(App::url('app/console/', $params, true), 10)
            );

        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        App::abort(400, $error);
    }

}
