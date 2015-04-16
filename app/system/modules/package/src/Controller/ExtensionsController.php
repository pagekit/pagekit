<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Exception;
use Pagekit\System\Event\ExtensionEvent;
use Pagekit\System\Extension;

/**
 * @Access("system: manage extensions", admin=true)
 */
class ExtensionsController
{
    /**
     * @Response("system:modules/package/views/extensions.php")
     */
    public function indexAction()
    {
        $packages = App::package()->getRepository('extension')->getPackages();

        foreach ($packages as $package) {
            $package->enabled = App::module($package->getName()) != null;
        }

        return [
            '$meta' => [
                'title' => __('Extensions')
            ],
            '$extensions' => [
                'api' => App::system()->config('api'),
                'packages' => $packages
            ]
        ];
    }

    /**
     * @Request({"name"}, csrf=true)
     * @Response("json")
     */
    public function enableAction($name)
    {
        try {

            ini_set('display_errors', 0);
            $handler = App::exception()->setHandler(function($exception) {

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
                throw new Exception(__('Unable to enable extension "%name%".', ['%name%' => $name]));
            }

            $extension->enable();

            App::config('system')->push('extensions', $extension->name);
            App::exception()->setHandler($handler);

            return ['message' => __('Extension enabled.')];

        } catch (Exception $e) {

            return ['message' => $e->getMessage(), 'error' => true];
        }
    }

    /**
     * @Request({"name"}, csrf=true)
     * @Response("json")
     */
    public function disableAction($name)
    {
        try {

            if (!$extension = App::module($name)) {
                throw new Exception(__('Extension "%name%" has not been loaded.', ['%name%' => $name]));
            }

            $extension->disable();

            App::config('system')->pull('extensions', $extension->name);
            App::module('system/cache')->clearCache();

            return ['message' => __('Extension disabled.')];

        } catch (Exception $e) {

            return ['message' => $e->getMessage(), 'error' => true];
        }
    }

    /**
     * @Request({"name"}, csrf=true)
     * @Response("json")
     */
    public function uninstallAction($name)
    {
        try {

            if (!App::module($name)) {
                App::module()->load($name);
            }

            if (!$extension = App::module($name)) {
                throw new Exception(__('Unable to uninstall extension "%name%".', ['%name%' => $name]));
            }

            $extension->disable();
            $extension->uninstall();

            App::package()->getInstaller('extension')->uninstall(App::package()->getRepository('extension')->findPackage($name));

            App::config('system')->pull('extensions', $extension->name);
            App::module('system/cache')->clearCache();

            return ['message' => __('Extension uninstalled.')];

        } catch (Exception $e) {

            return ['message' => $e->getMessage(), 'error' => true];
        }
    }
}
