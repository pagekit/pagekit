<?php

namespace Pagekit\System\Controller;

use Pagekit\Component\Auth\Auth;
use Pagekit\Component\Auth\RememberMe;
use Pagekit\Component\Routing\Event\GenerateUrlEvent;
use Pagekit\Framework\Controller\Controller;
use Pagekit\System\Event\RegisterTmplEvent;

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

        return array('head.title' => __('Login'), 'last_username' => $this('session')->get(Auth::LAST_USERNAME), 'redirect' => $this('request')->get('redirect') ? : $this->url('@system/system/admin', array(), true), 'remember_me_param' => RememberMe::REMEMBER_ME_PARAM);
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
        $this('events')->on('url.generate', function(GenerateUrlEvent $event) { $event->stopPropagation(); }, 32);

        if (!$url || !$url[0] == '@' || !$url = $this->url($url)) {
            return $this('response')->json(array('error' => true, 'message' => __('Invalid URL.')));
        }

        return $this('response')->json(array('url' => ltrim(substr($url, strlen($this('request')->getBaseUrl())), '/')));
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
