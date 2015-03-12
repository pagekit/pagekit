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
        if ($frontpage = App::system()->config('frontpage')) {
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
            $config = App::option('system:config', []);
            $config['frontpage'] = $node->get('url');
            App::option()->set('system:config', $config, true);
        }
    }

    public function onDelete(EntityEvent $event) {
        if ($event->getEntity()->get('frontpage')) {
            $config = App::option('system:config', []);
            unset($config['frontpage']);
            App::option()->set('system:config', $config, true);
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
