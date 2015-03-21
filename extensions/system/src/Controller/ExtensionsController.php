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
     * @Response("extensions/system/views/admin/extensions.php")
     */
    public function indexAction()
    {
        $packages = App::package()->getRepository('extension')->getPackages();

        foreach ($packages as $package) {
            $package->enabled = App::module($package->getName()) != null;
        }

        App::view()->meta(['title' => __('Extensions')]);
        App::view()->script('extensions-index', 'extensions/system/app/extensions.js', 'marketplace');
        App::view()->data('extensions', ['api' => App::system()->config('api'), 'packages' => $packages]);
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

            $config = App::option('system/core:config', ['extensions' => []]);
            $config['extensions'] = array_unique(array_merge($config['extensions'], [$extension->name]));
            App::option()->set('system/core:config', $config, true);

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

            $this->disable($extension);

            $extension->uninstall();

            App::package()->getInstaller('extension')->uninstall(App::package()->getRepository('extension')->findPackage($name));
            App::module('system/cache')->clearCache();

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

            return App::tmpl($tmpl, ['head.title' => __('%extension% Settings', ['%extension%' => $title]), 'extension' => $extension, 'params' => $event->getParams()]);

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

            App::option()->set("$name:config", $event->getParams(), true);
            App::message()->success(__('Settings saved.'));

            return $this->redirect('@system/extensions/settings', compact('name'));

        } catch (Exception $e) {

            App::message()->error($e->getMessage());
        }

        return $this->redirect('@system/system');
    }

    protected function disable(Extension $extension)
    {
        $config = App::option('system/core:config', ['extensions' => []]);
        $config['extensions'] = array_values(array_diff($config['extensions'], [$extension->getName()]));
        App::option()->set('system/core:config', $config, true);

        $extension->disable();
    }
}
