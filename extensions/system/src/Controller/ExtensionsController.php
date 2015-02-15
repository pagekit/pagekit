<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Application\Exception;
use Pagekit\System\Event\ExtensionEvent;
use Pagekit\System\Extension;

/**
 * @Access("system: manage extensions", admin=true)
 */
class ExtensionsController extends Controller
{
    /**
     * @Response("extensions/system/views/admin/extensions/index.razr")
     */
    public function indexAction()
    {
        $packages = [];
        $packagesJson = [];

        foreach (App::package()->getRepository('extension')->getPackages() as $package) {
            if ('system' != $name = $package->getName()) {
                $packages[$name] = $package;
                $packagesJson[$name] = $package->getVersion();
            }
        }

        if (App::request()->isXmlHttpRequest()) {
            return App::response()->json([
                'table' => App::view('extensions/system/views/admin/extensions/table.razr', compact('packages'))
            ]);
        }

        return ['head.title' => __('Extensions'), 'api' => App::system()->config('api.url'), 'key' => App::system()->config('api.key'), 'packages' => $packages, 'packagesJson' => json_encode($packagesJson)];
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

                if (App::module('framework')->config('debug')) {
                    $message .= '<br><br>'.$exception->getMessage();
                }

                App::response()->json(['error' => true, 'message' => $message])->send();
            });

            App::module()->load($name);

            if (!$extension = App::module($name)) {
                throw new Exception(__('Unable to enable extension "%name%".', ['%name%' => $name]));
            }

            $extension->enable();

            $settings = App::option('system/core:settings', ['extensions' => []]);
            $settings['extensions'] = array_unique(array_merge($settings['extensions'], [$extension->name]));
            App::option()->set('system/core:settings', $settings, true);

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

            $this->disable($extension);

            App::system()->clearCache();

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

            $this->disable($extension);

            $extension->uninstall();

            App::package()->getInstaller('extension')->uninstall(App::package()->getRepository('extension')->findPackage($name));
            App::system()->clearCache();

            return ['message' => __('Extension uninstalled.')];

        } catch (Exception $e) {

            return ['message' => $e->getMessage(), 'error' => true];
        }
    }

    /**
     * @Request({"name"})
     */
    public function settingsAction($name)
    {
        try {

            if (!$extension = App::module($name) or !$tmpl = $extension->config('settings.view')) {
                throw new Exception(__('Invalid extension.'));
            }

            $event = App::trigger('system.extension.edit', new ExtensionEvent($extension, $extension->config));
            $title = App::package()->getRepository('extension')->findPackage($extension->getName())->getTitle();

            return App::view($tmpl, ['head.title' => __('%extension% Settings', ['%extension%' => $title]), 'extension' => $extension, 'params' => $event->getParams()]);

        } catch (Exception $e) {
            App::message()->error($e->getMessage());
        }

        return $this->redirect('@system/system');
    }

    /**
     * @Request({"name", "params": "array"})
     */
    public function saveSettingsAction($name, $params = [])
    {
        try {

            if (!$extension = App::module($name)) {
                throw new Exception(__('Invalid extension.'));
            }

            $event = App::trigger('system.extension.save', new ExtensionEvent($extension, $params));

            App::option()->set("$name:settings", $event->getParams(), true);
            App::message()->success(__('Settings saved.'));

            return $this->redirect('@system/extensions/settings', compact('name'));

        } catch (Exception $e) {

            App::message()->error($e->getMessage());
        }

        return $this->redirect('@system/system');
    }

    protected function disable(Extension $extension)
    {
        $settings = App::option('system/core:settings', ['extensions' => []]);
        $settings['extensions'] = array_values(array_diff($settings['extensions'], [$extension->getName()]));
        App::option()->set('system/core:settings', $settings, true);

        $extension->disable();
    }
}
