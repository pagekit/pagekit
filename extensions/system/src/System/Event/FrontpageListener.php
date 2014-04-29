<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\EventSubscriber;

class FrontpageListener extends EventSubscriber
{
    /**
     * Registers frontpage route
     */
    public function onInit()
    {
        if ($frontpage = $this('config')->get('app.frontpage')) {
            $app = self::$app;
            $this('router')->getUrlAliases()->register('/', $this('system.info')->resolveUrl($frontpage));
            $this('router')->get('/', '@frontpage', function() use ($app) {
                $app->abort(404);
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'init' => array('onInit', 8)
        );
    }
}
