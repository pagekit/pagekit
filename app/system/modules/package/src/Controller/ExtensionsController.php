<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\System\Extension;

/**
 * @Access("system: manage extensions", admin=true)
 */
class ExtensionsController
{
    public function indexAction()
    {
        $packages = App::package()->getRepository('extension')->getPackages();

        foreach ($packages as $package) {
            $package->enabled = App::module($package->getName()) != null;
        }

        return [
            '$view' => [
                'title' => __('Extensions'),
                'name'  => 'system:modules/package/views/extensions.php'
            ],
            '$extensions' => [
                'api' => App::system()->config('api'),
                'packages' => $packages
            ]
        ];
    }

    /**
     * @Request({"name"}, csrf=true)
     */
    public function enableAction($name)
    {
        ini_set('display_errors', 0);

        $handler = App::exception()->setHandler(function ($exception) {

            while (ob_get_level()) {
                ob_get_clean();
            }

            $message = __('Unable to activate extension.<br>The extension triggered a fatal error.');

            if (App::module('application')->config('debug')) {
                $message .= '<br><br>'.$exception->getMessage();
            }

            App::response()->json(['error' => true, 'message' => $message])->send();
        });

        App::module()->load($name);

        if (!$extension = App::module($name)) {
            App::abort(400, __('Unable to enable extension "%name%".', ['%name%' => $name]));
        }

        $extension->enable();

        App::config('system')->push('extensions', $extension->name);
        App::exception()->setHandler($handler);

        return ['message' => __('Extension enabled.')];
    }

    /**
     * @Request({"name"}, csrf=true)
     */
    public function disableAction($name)
    {
        if (!$extension = App::module($name)) {
            App::abort(400, __('Extension "%name%" has not been loaded.', ['%name%' => $name]));
        }

        $extension->disable();

        App::config('system')->pull('extensions', $extension->name);
        App::module('system/cache')->clearCache();

        return ['message' => __('Extension disabled.')];
    }

    /**
     * @Request({"name"}, csrf=true)
     */
    public function uninstallAction($name)
    {
        if (!App::module($name)) {
            App::module()->load($name);
        }

        if (!$extension = App::module($name)) {
            App::abort(400, __('Unable to uninstall extension "%name%".', ['%name%' => $name]));
        }

        $extension->disable();
        $extension->uninstall();

        App::package()->getInstaller('extension')->uninstall(App::package()->getRepository('extension')->findPackage($name));

        App::config('system')->pull('extensions', $extension->name);
        App::module('system/cache')->clearCache();

        return ['message' => __('Extension uninstalled.')];
    }
}
