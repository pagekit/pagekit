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
     * @Access(admin=true)
     * @View("system/admin/settings/index.razr.php")
     */
    public function indexAction()
    {
        $packages = array();
        foreach ($this('extensions') as $extension) {
            if ($extension->getConfig('settings')) {
                $packages[$extension->getName()] = $this('extensions')->getRepository()->findPackage($extension->getName());
            }
        }

        return array('head.title' => __('Settings'), 'user' => $this('user'), 'packages' => $packages);
    }

    /**
     * @Route("/admin/login", methods="POST", options={"maintenance"=true})
     * @View("extension://system/theme/templates/login.razr.php", layout=false)
     */
    public function loginAction()
    {
        if ($this('user')->isAuthenticated()) {
            return $this->redirect('@system/system/admin');
        }

        $lastLogin = $this('session')->get(ResetPasswordController::RESET_LOGIN);
        $this('session')->remove(ResetPasswordController::RESET_LOGIN);

        return array('head.title' => __('Login'), 'last_username' => $this('session')->get(Auth::LAST_USERNAME), 'redirect' => $this('request')->get('redirect') ? : $this('url')->route('@system/system/admin', array(), true), 'remember_me_param' => RememberMe::REMEMBER_ME_PARAM, 'last_login' => $lastLogin);
    }

    /**
     * @Route("/")
     * @Access(admin=true)
     */
    public function adminAction()
    {
        return $this->redirect('@system/dashboard/index');
    }

    /**
     * @Route("/system/storage")
     * @Access("system: manage storage", admin=true)
     * @View("system/admin/settings/storage.razr.php")
     */
    public function storageAction()
    {
        return array('head.title' => __('Storage'), 'root' => $this('config')->get('app.storage'), 'mode' => 'write');
    }

    /**
     * @Route("/system/info")
     * @Access(admin=true)
     * @View("system/admin/settings/info.razr.php")
     */
    public function infoAction()
    {
        return array('head.title' => __('System Information'), 'info' => $this('system.info')->get());
    }

    /**
     * @Route("/system/locale")
     */
    public function localeAction()
    {
        $this('events')->dispatch('system.locale', $event = new LocaleEvent);

        return $this('response')->json($event->getMessages());
    }

    /**
     * @Route("/system/tmpl/{templates}")
     */
    public function tmplAction($templates = '')
    {
        $response = array();
        $event = $this('events')->dispatch('system.tmpl', new TmplEvent);

        foreach (explode(',', $templates) as $template) {
            if ($event->has($template)) {
                $response[$template] = $this('view')->render($event->get($template));
            }
        }

        return $this('response')->json($response);
    }

    /**
     * @Route("/system/clearcache")
     * @Access(admin=true)
     * @Request({"caches": "array"})
     * @Token
     */
    public function clearCacheAction($caches)
    {
        $this('system')->clearCache($caches);

        return $this('request')->isXmlHttpRequest() ? $this('response')->json(array('message' => __('Cache cleared!'))) : $this->redirect('@system/system/index');
    }

    /**
     * @Route("/admin/menu", methods="POST", options={})
     * @Access(admin=true)
     * @Request({"order": "array"})
     */
    public function adminMenuAction($order)
    {

        try {

            if (!$order) {
                throw new Exception('Missing order data.');
            }

            $user = $this('user');

            $user->set('admin.menu', $order);

            //$this('users')->getUserRepository()->save($user);

            $response = array('message' => __('Order saved.'));

        } catch (Exception $e) {
            $response = array('message' => $e->getMessage(), 'error' => true);
        }

        return $this('response')->json($response);
    }
}
