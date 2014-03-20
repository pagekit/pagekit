<?php

namespace Pagekit\System\Event;

use Pagekit\Component\Routing\Event\GenerateRouteEvent;
use Pagekit\Framework\Event\EventSubscriber;

class FrontpageListener extends EventSubscriber
{
    /**
     * Registers frontpage route
     */
    public function onBoot()
    {
        if ($frontpage = $this('option')->get('system:app.frontpage')) {
            $this('router')->getUrlAliases()->register('/', $this('url')->route($frontpage));
        }

        $this('router')->get('/', '@frontpage', function() {});
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'boot' => array('onBoot', 8)
        );
    }
}
