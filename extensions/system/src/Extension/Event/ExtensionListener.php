<?php

namespace Pagekit\Extension\Event;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\System\Package\Event\LoadFailureEvent;
use Pagekit\System\Package\Exception\ExtensionLoadException;

class ExtensionListener extends EventSubscriber
{
    /**
     * Loads the extensions.
     */
    public function onKernelRequest($event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $app = self::$app;

        foreach (array_unique($app['extensions.boot']) as $extension) {
            try {
                $app['extensions']->load($extension)->boot($app);
            } catch (ExtensionLoadException $e) {
                $app['events']->dispatch('extension.load_failure', new LoadFailureEvent($extension));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => array('onKernelRequest', 70)
        );
    }
}
