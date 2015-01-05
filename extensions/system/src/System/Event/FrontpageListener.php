<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Database\Event\EntityEvent;
use Pagekit\Framework\Event\EventSubscriber;

class FrontpageListener extends EventSubscriber
{
    /**
     * Registers frontpage route.
     */
    public function onSystemInit()
    {
        if ($frontpage = $this['config']->get('app.frontpage')) {
            $this['aliases']->add('/', $frontpage);
        } else {
            $this['callbacks']->get('/', '_frontpage', function() use ($frontpage) {
                return __('No Frontpage assigned.');
            });
        }
    }

    public function onSave(EntityEvent $event) {
        $node = $event->getEntity();
        if ($node->get('homepage')) {
            $this['option']->set('system:app.frontpage', $node->get('url'));
        }
    }

    public function onDelete(EntityEvent $event) {
        if ($event->getEntity()->get('homepage')) {
            $this['option']->set('system:app.frontpage', null);
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
