<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Application\Exception;
use Pagekit\System\Event\LocaleEvent;
use Pagekit\System\Event\TmplEvent;
use Pagekit\User\Entity\User;

/**
 * @Route("/")
 */
class SystemController extends Controller
{
    /**
     * @Access(admin=true)
     * @Response("extensions/system/views/admin/settings/index.razr")
     */
    public function indexAction()
    {
        $packages = [];

        foreach (App::option('system:extensions', []) as $name) {
            if ($extension = App::module($name) and $extension->config('settings.view')) {
                $packages[$extension->name] = App::package()->getRepository('extension')->findPackage($extension->name);
            }
        }

        return ['head.title' => __('Settings'), 'user' => App::user(), 'packages' => $packages];
    }

    /**
     * @Access(admin=true)
     * @Request({"order": "array"})
     * @Response("json")
     */
    public function adminMenuAction($order)
    {
        try {

            if (!$order) {
                throw new Exception('Missing order data.');
            }

            $user = User::find(App::user()->getId());
            $user->set('admin.menu', $order);
            $user->save();

            return ['message' => __('Order saved.')];

        } catch (Exception $e) {

            return ['message' => $e->getMessage(), 'error' => true];
        }
    }

    /**
     * @Access("system: manage storage", admin=true)
     * @Response("extensions/system/views/admin/settings/storage.razr")
     */
    public function storageAction()
    {
        return ['head.title' => __('Storage'), 'root' => App::config('app.storage'), 'mode' => 'write'];
    }

    /**
     * @Access(admin=true)
     * @Response("extensions/system/views/admin/settings/info.razr")
     */
    public function infoAction()
    {
        return ['head.title' => __('System Information'), 'info' => App::systemInfo()->get()];
    }

    /**
     * @Response("json")
     */
    public function localeAction()
    {
        App::trigger('system.locale', $event = new LocaleEvent);

        return $event->getMessages();
    }

    /**
     * @Route("/tmpl/{templates}")
     * @Response("json")
     */
    public function tmplAction($templates = '')
    {
        $response = [];
        $event = App::trigger('system.tmpl', new TmplEvent);

        foreach (explode(',', $templates) as $template) {
            if ($event->has($template)) {
                $response[$template] = App::view($event->get($template));
            }
        }

        return $response;
    }

    /**
     * @Access(admin=true)
     * @Request({"caches": "array"}, csrf=true)
     * @Response("json")
     */
    public function clearCacheAction($caches)
    {
        App::module('system')->clearCache($caches);

        return App::request()->isXmlHttpRequest() ? ['message' => __('Cache cleared!')] : $this->redirect('@system/system');
    }
}
