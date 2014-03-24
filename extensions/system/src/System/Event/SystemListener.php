<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\System\Package\Event\LoadFailureEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class SystemListener extends EventSubscriber
{
    /**
     * Dispatches boot event.
     */
    public function onEarlyKernelRequest(GetResponseEvent $event, $name, $dispatcher)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        self::$app['isAdmin'] = (bool) preg_match('#^/admin(/?$|/.+)#', $event->getRequest()->getPathInfo());

        $dispatcher->dispatch('boot', new SystemInitEvent($this->getApplication()));
    }

    /**
     * Dispatches initialize events.
     */
    public function onKernelRequest(GetResponseEvent $event, $name, $dispatcher)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $dispatcher->dispatch('init', new SystemInitEvent(self::$app));
        $dispatcher->dispatch($this('isAdmin') ? 'admin.init' : 'site.init', new SystemInitEvent(self::$app));
    }

    /**
     * Deactivate extension on load failure.
     *
     * @param LoadFailureEvent $event
     */
    public function onExtensionLoadException(LoadFailureEvent $event)
    {
        $extensions = $this('option')->get('system:extensions', array());

        if (false !== $index = array_search($event->getExtensionName(), $extensions)) {
            unset($extensions[$index]);
            $extensions = array_values($extensions);
        }

        $this('option')->set('system:extensions', $extensions);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => array(
                array('onEarlyKernelRequest', 64),
                array('onKernelRequest', 30)
            ),
            'extension.load_failure' => 'onExtensionLoadException'
        );
    }
}
