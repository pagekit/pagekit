<?php

namespace Pagekit\Extension\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Application\Exception;
use Pagekit\Extension\Event\ExtensionEvent;
use Pagekit\Extension\Extension;

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

        foreach (App::extension()->getRepository()->getPackages() as $package) {
            if (!$this->isCore($name = $package->getName())) {
                $packages[$name] = $package;
                $packagesJson[$name] = $package->getVersion();
            }
        }

        if (App::request()->isXmlHttpRequest()) {
            return App::response()->json([
                'table' => App::view('extensions/system/views/admin/extensions/table.razr', compact('packages'))
            ]);
        }

        return ['head.title' => __('Extensions'), 'api' => App::config('api.url'), 'key' => App::option('system:api.key'), 'packages' => $packages, 'packagesJson' => json_encode($packagesJson)];
    }

    /**
     * @Request({"name"}, csrf=true)
     * @Response("json")
     */
    public function enableAction($name)
    {
        try {

            if ($this->isCore($name)) {
                throw new Exception(__('Core extensions may not be enabled.'));
            }

            ini_set('display_errors', 0);
            $handler = App::exception()->setHandler(function($exception) {

                while (ob_get_level()) {
                    ob_get_clean();
                }

                $message = __('Unable to activate extension.<br>The extension triggered a fatal error.');

                if (App::config('app.debug')) {
                    $message .= '<br><br>'.$exception->getMessage();
                }

                App::response()->json(['error' => true, 'message' => $message])->send();
            });

            if (!App::extension($name)) {
                App::extension()->load($name);
            }

            if (!$extension = App::extension($name)) {
                throw new Exception(__('Unable to enable extension "%name%".', ['%name%' => $name]));
            }

            $extension->enable();
            $extension->boot(App::getInstance());

            App::option()->set('system:extensions', array_unique(array_merge(App::option('system:extensions', []), [$extension->getName()])), true);

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

            if ($this->isCore($name)) {
                throw new Exception(__('Core extensions may not be disabled.'));
            }

            if (!$extension = App::extension($name)) {
                throw new Exception(__('Extension "%name%" has not been loaded.', ['%name%' => $name]));
            }

            $this->disable($extension);

            App::extension('system')->clearCache();

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

            if ($this->isCore($name)) {
                throw new Exception(__('Core extensions may not be uninstalled.'));
            }

            if (!App::extension($name)) {
                App::extension()->load($name);
            }

            if (!$extension = App::extension($name)) {
                throw new Exception(__('Unable to uninstall extension "%name%".', ['%name%' => $name]));
            }

            $this->disable($extension);

            $extension->uninstall();

            App::extension()->getInstaller()->uninstall(App::extension()->getRepository()->findPackage($name));
            App::extension('system')->clearCache();

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

            if (!$extension = App::extension($name) or !$tmpl = $extension->getConfig('parameters.settings.view')) {
                throw new Exception(__('Invalid extension.'));
            }

            $event = App::trigger('system.extension.edit', new ExtensionEvent($extension, $extension->getParams()));
            $title = App::extension()->getRepository()->findPackage($extension->getName())->getTitle();

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

            if (!$extension = App::extension($name)) {
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
        App::option()->set('system:extensions', array_values(array_diff(App::option('system:extensions', []), [$extension->getName()])), true);

        $extension->disable();
    }

    protected function isCore($name)
    {
        return in_array($name, App::config('extension.core', []));
    }
}
