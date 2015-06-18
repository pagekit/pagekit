<?php

namespace Pagekit\System\Controller;

use GuzzleHttp\Client;
use Pagekit\Application as App;
use Pagekit\System\Package\Package;
use Pagekit\System\Package\PackageInstaller;

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
                'name'  => 'system/package:views/update.php'
            ],
            '$config' => [
                'api' => App::module('system/package')->config('api.url'),
                'channel' => App::module('system/package')->config('release_channel'),
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
            $installer = new PackageInstaller($client);

            $package = new Package([]);
            $package->set('dist', $update);

            $installer->download($package, $path);

            return ['message' => 'success'];

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

        return ['message' => 'success'];
    }

    public function databaseAction()
    {
        if (!$update = App::session()->get('system.update')) {
            App::abort(400, __('You may not call this step directly.'));
        }

        App::system()->enable();
        App::module('system/cache')->clearCache();
        App::session()->remove('system.update');

        return ['message' => 'success'];
    }
}
