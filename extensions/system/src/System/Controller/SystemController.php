<?php

namespace Pagekit\System\Controller;

use Pagekit\Component\Auth\Auth;
use Pagekit\Component\Auth\RememberMe;
use Pagekit\Framework\Controller\Controller;
use Pagekit\System\Event\LocaleEvent;
use Pagekit\System\Event\TmplEvent;
use Pagekit\User\Controller\ResetPasswordController;

/**
 * @Route("/")
 */
class SystemController extends Controller
{
    /**
     * @Route("/system")
     * @Response("extension://system/views/admin/settings/index.razr")
     * @Access(admin=true)
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
     * @Route("/admin/login", methods="POST", defaults={"_maintenance"=true})
     * @Response("extension://system/theme/templates/login.razr", layout=false)
     */
    public function loginAction()
    {
        if ($this['user']->isAuthenticated()) {
            return $this->redirect('@system/system/admin');
        }

        return ['head.title' => __('Login'), 'last_username' => $this['session']->get(Auth::LAST_USERNAME), 'redirect' => $this['request']->get('redirect') ? : $this['url']->route('@system/system/admin', [], true), 'remember_me_param' => RememberMe::REMEMBER_ME_PARAM];
    }

    /**
     * @Route("/")
     * @Access(admin=true)
     */
    public function adminAction()
    {
        return $this->redirect('@system/dashboard');
    }

    /**
     * @Route("/admin/menu")
     * @Request({"order": "array"})
     * @Response("json")
     * @Access(admin=true)
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
     * @Route("/system/storage")
     * @Response("extension://system/views/admin/settings/storage.razr")
     * @Access("system: manage storage", admin=true)
     */
    public function storageAction()
    {
        return ['head.title' => __('Storage'), 'root' => $this['config']->get('app.storage'), 'mode' => 'write'];
    }

    /**
     * @Route("/system/info")
     * @Response("extension://system/views/admin/settings/info.razr")
     * @Access(admin=true)
     */
    public function infoAction()
    {
        return ['head.title' => __('System Information'), 'info' => $this['system.info']->get()];
    }

    /**
     * @Route("/system/locale")
     * @Response("json")
     */
    public function localeAction()
    {
        $this['events']->dispatch('system.locale', $event = new LocaleEvent);

        return $event->getMessages();
    }

    /**
     * @Route("/system/tmpl/{templates}")
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
     * @Route("/system/clearcache")
     * @Request({"caches": "array"}, csrf=true)
     * @Response("json")
     * @Access(admin=true)
     */
    public function clearCacheAction($caches)
    {
        $this['system']->clearCache($caches);

        return $this['request']->isXmlHttpRequest() ? ['message' => __('Cache cleared!')] : $this->redirect('@system/system');
    }
}
