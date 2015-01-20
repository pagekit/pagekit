<?php

namespace Pagekit\System\Event;

use Pagekit\Application as App;
use Pagekit\Database\Event\EntityEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FrontpageListener implements EventSubscriberInterface
{
    /**
     * Registers frontpage route.
     */
    public function onSystemInit()
    {
        if ($frontpage = App::config('app.frontpage')) {
            App::aliases()->add('/', $frontpage);
        } else {
            App::callbacks()->get('/', '_frontpage', function() {
                return __('No Frontpage assigned.');
            });
        }
    }

    public function onSave(EntityEvent $event) {
        $node = $event->getEntity();
        if ($node->get('frontpage')) {
            App::option()->set('system:app.frontpage', $node->get('url'), true);
        }
    }

    public function onDelete(EntityEvent $event) {
        if ($event->getEntity()->get('frontpage')) {
            App::option()->remove('system:app.frontpage');
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.init'          => ['onSystemInit', -15],
            'system.node.postSave'   => 'onSave',
            'system.node.postDelete' => 'onDelete'
        ];
    }
}
