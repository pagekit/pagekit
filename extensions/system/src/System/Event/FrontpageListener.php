<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\EventSubscriber;

class FrontpageListener extends EventSubscriber
{
    /**
     * Registers frontpage route.
     */
    public function onSystemInit()
    {
        if ($frontpage = $this['config']->get('app.frontpage')) {
            $this['router']->addAlias('/', $frontpage);
        } else {
            $this['controllers']->get('/', '_frontpage', function() use ($frontpage) {
                return __('No Frontpage assigned.');
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.init' => ['onSystemInit', -15]
        ];
    }
}
