<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
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

        foreach (App::extension() as $extension) {
            if ($extension->getConfig('parameters.settings.view')) {
                $packages[$extension->getName()] = App::extension()->getRepository()->findPackage($extension->getName());
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
        return ['head.title' => __('Storage'), 'root' => App::config()->get('app.storage'), 'mode' => 'write'];
    }

    /**
     * @Access(admin=true)
     * @Response("extensions/system/views/admin/settings/info.razr")
     */
    public function infoAction()
    {
        return ['head.title' => __('System Information'), 'info' => App::get('system.info')->get()];
    }

    /**
     * @Response("json")
     */
    public function localeAction()
    {
        App::events()->dispatch('system.locale', $event = new LocaleEvent);

        return $event->getMessages();
    }

    /**
     * @Route("/tmpl/{templates}")
     * @Response("json")
     */
    public function tmplAction($templates = '')
    {
        $response = [];
        $event = App::events()->dispatch('system.tmpl', new TmplEvent);

        foreach (explode(',', $templates) as $template) {
            if ($event->has($template)) {
                $response[$template] = App::view()->render($event->get($template));
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
        App::system()->clearCache($caches);

        return App::request()->isXmlHttpRequest() ? ['message' => __('Cache cleared!')] : $this->redirect('@system/system');
    }
}
