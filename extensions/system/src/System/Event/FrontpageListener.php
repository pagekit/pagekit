<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Menu\Event\ActiveMenuEvent;

class FrontpageListener extends EventSubscriber
{
    /**
     * Registers frontpage route
     */
    public function onSystemInit()
    {
        if ($frontpage = $this('config')->get('app.frontpage')) {
            $this('router')->getUrlAliases()->add('/', $frontpage);
        }
        $this('router')->get('/', '@frontpage', function() {});
    }

    /**
     * Activates frontpage menu items
     *
     * @param ActiveMenuEvent $event
     */
    public function onSystemMenu(ActiveMenuEvent $event)
    {
        if ($this('request')->getPathInfo() == '/') {
            foreach ($event->get('@frontpage') as $id => $item) {
                $event->add($id);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'system.init' => array('onSystemInit', -15),
            'system.menu' => 'onSystemMenu'
        );
    }
}
