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
        if ($frontpage = App::config()->get('app.frontpage')) {
            App::aliases()->add('/', $frontpage);
        } else {
            App::callbacks()->get('/', '_frontpage', function() {
                return __('No Frontpage assigned.');
            });
        }
    }

    public function onSave(EntityEvent $event) {
        $node = $event->getEntity();
        if ($node->get('homepage')) {
            App::option()->set('system:app.frontpage', $node->get('url'));
        }
    }

    public function onDelete(EntityEvent $event) {
        if ($event->getEntity()->get('homepage')) {
            App::option()->set('system:app.frontpage', null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.init'          => ['onSystemInit', -15],
            'tree.node.postSave'   => 'onSave',
            'tree.node.postDelete' => 'onDelete'
        ];
    }
}
