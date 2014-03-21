<?php

namespace Pagekit\System\Controller;

use Pagekit\Component\Auth\Auth;
use Pagekit\Component\Auth\RememberMe;
use Pagekit\Framework\Controller\Controller;
use Pagekit\System\Event\RegisterJsonEvent;
use Pagekit\System\Event\RegisterTmplEvent;
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
        return array('head.title' => __('Settings'), 'user' => $this('user'));
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
     * @Route("/system/resolveurl")
     * @Access(admin=true)
     * @Request({"url"})
     */
    public function resolveUrlAction($url)
    {
        $url = $this('system.info')->resolveURL($url);

        $url = $url !== '' ? $url : __('Frontpage');

        return $this('response')->json(false !== $url ? compact('url') : array('error' => true, 'message' => __('Invalid URL.')));
    }

    /**
     * @Route("/system/tmpl/{templates}")
     */
    public function tmplAction($templates = '')
    {
        $this('events')->trigger('view.register.tmpl', $event = new RegisterTmplEvent);

        $response = array();

        foreach (explode(',', $templates) as $template) {
            if ($event->has($template)) {
                $response[$template] = $this('view')->render($event->get($template));
            }
        }

        return $this('response')->json($response);
    }


    /**
     * @Route("/system/json/{sources}")
     */
    public function jsonAction($sources = '')
    {

        $this('events')->trigger('view.register.json', $event = new RegisterJsonEvent);

        $response = array();

        foreach (explode(',', $sources) as $source) {
            if ($event->has($source)) {
                $response[$source] = json_decode($this('view')->render($event->get($source)));
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
}
