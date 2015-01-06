<?php

namespace Pagekit\System\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\System\Event\LocaleEvent;
use Pagekit\System\Event\TmplEvent;

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

        foreach ($this['extensions'] as $extension) {
            if ($extension->getConfig('parameters.settings.view')) {
                $packages[$extension->getName()] = $this['extensions']->getRepository()->findPackage($extension->getName());
            }
        }

        return ['head.title' => __('Settings'), 'user' => $this['user'], 'packages' => $packages];
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

            $user = $this['users']->get($this['user']->getId());
            $user->set('admin.menu', $order);

            $this['users']->getUserRepository()->save($user);

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
        return ['head.title' => __('Storage'), 'root' => $this['config']->get('app.storage'), 'mode' => 'write'];
    }

    /**
     * @Access(admin=true)
     * @Response("extensions/system/views/admin/settings/info.razr")
     */
    public function infoAction()
    {
        return ['head.title' => __('System Information'), 'info' => $this['system.info']->get()];
    }

    /**
     * @Response("json")
     */
    public function localeAction()
    {
        $this['events']->dispatch('system.locale', $event = new LocaleEvent);

        return $event->getMessages();
    }

    /**
     * @Route("/tmpl/{templates}")
     * @Response("json")
     */
    public function tmplAction($templates = '')
    {
        $response = [];
        $event = $this['events']->dispatch('system.tmpl', new TmplEvent);

        foreach (explode(',', $templates) as $template) {
            if ($event->has($template)) {
                $response[$template] = $this['view']->render($event->get($template));
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
        $this['system']->clearCache($caches);

        return $this['request']->isXmlHttpRequest() ? ['message' => __('Cache cleared!')] : $this->redirect('@system/system');
    }
}
