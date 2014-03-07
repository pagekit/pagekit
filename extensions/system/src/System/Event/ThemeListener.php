<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\EventSubscriber;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ThemeListener extends EventSubscriber
{
    /**
     * Loads the site/admin theme.
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        try {

            self::$app['theme.admin'] = $this('themes')->load('system', 'extension://system/theme');
            self::$app['theme.site']  = $this('themes')->load($this('config')->get('theme.site'));

        } catch(\Exception $e) {}
    }

    /**
     * Sets the site theme layout and registers theme's positions.
     */
    public function onSiteInit()
    {
        if ($theme = $this('theme.site')) {
            $this('view')->setLayout($theme->boot(self::$app)->getLayout());
        }
    }

    /**
     * Sets the admin theme layout.
     */
    public function onAdminInit()
    {
        $this('view')->setLayout($this('theme.admin')->boot(self::$app)->getLayout());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => array('onKernelRequest', 128),
            'admin.init'     => 'onAdminInit',
            'site.init'      => 'onSiteInit'
        );
    }
}
