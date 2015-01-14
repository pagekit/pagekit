<?php

namespace Pagekit\Extension\Controller;

use Pagekit\Application as App;
use Pagekit\Extension\Event\ExtensionEvent;
use Pagekit\Extension\Extension;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;

/**
 * @Access("system: manage extensions", admin=true)
 */
class ExtensionsController extends Controller
{
    protected $extensions;
    protected $api;
    protected $apiKey;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->extensions = App::extensions();
        $this->api        = App::config()->get('api.url');
        $this->apiKey     = App::option()->get('system:api.key');
    }

    /**
     * @Response("extensions/system/views/admin/extensions/index.razr")
     */
    public function indexAction()
    {
        $packages = [];
        $packagesJson = [];

        foreach ($this->extensions->getRepository()->getPackages() as $package) {
            if (!$this->isCore($name = $package->getName())) {
                $packages[$name] = $package;
                $packagesJson[$name] = $package->getVersion();
            }
        }

        if (App::request()->isXmlHttpRequest()) {
            return App::response()->json([
                'table' => App::view()->render('extensions/system/views/admin/extensions/table.razr', ['packages' => $packages])
            ]);
        }

        return ['head.title' => __('Extensions'), 'api' => $this->api, 'key' => $this->apiKey, 'packages' => $packages, 'packagesJson' => json_encode($packagesJson)];
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

                if (App::config()['app.debug']) {
                    $message .= '<br><br>'.$exception->getMessage();
                }

                App::response()->json(['error' => true, 'message' => $message])->send();
            });

            if (!$this->extensions->get($name)) {
                $this->extensions->load($name);
            }

            if (!$extension = $this->extensions->get($name)) {
                throw new Exception(__('Unable to enable extension "%name%".', ['%name%' => $name]));
            }

            $extension->enable();

            $extension->boot(App::getInstance());

            App::option()->set('system:extensions', array_unique(array_merge(App::option()->get('system:extensions', []), [$extension->getName()])), true);

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

            if (!$extension = $this->extensions->get($name)) {
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

            if ($this->isCore($name)) {
                throw new Exception(__('Core extensions may not be uninstalled.'));
            }

            if (!$this->extensions->get($name)) {
                $this->extensions->load($name);
            }

            if (!$extension = $this->extensions->get($name)) {
                throw new Exception(__('Unable to uninstall extension "%name%".', ['%name%' => $name]));
            }

            $this->disable($extension);

            $extension->uninstall();

            $this->extensions->getInstaller()->uninstall($this->extensions->getRepository()->findPackage($name));

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

            if (!$extension = $this->extensions->get($name) or !$tmpl = $extension->getConfig('parameters.settings.view')) {
                throw new Exception(__('Invalid extension.'));
            }

            $event = App::events()->dispatch('system.extension.edit', new ExtensionEvent($extension, $extension->getParams()));

            $title = $this->extensions->getRepository()->findPackage($extension->getName())->getTitle();

            return App::view()->render($tmpl, ['head.title' => __('%extension% Settings', ['%extension%' => $title]), 'extension' => $extension, 'params' => $event->getParams()]);

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

            if (!$extension = $this->extensions->get($name)) {
                throw new Exception(__('Invalid extension.'));
            }

            $event = App::events()->dispatch('system.extension.save', new ExtensionEvent($extension, $params));

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
        App::option()->set('system:extensions', array_values(array_diff(App::option()->get('system:extensions', []), [$extension->getName()])), true);

        $extension->disable();
    }

    protected function isCore($name)
    {
        return in_array($name, App::config()->get('extension.core', []));
    }
}
