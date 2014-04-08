<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\EventSubscriber;

class LocaleListener extends EventSubscriber
{
    /**
     * Sets the systems locale
     */
    public function onInit()
    {
        $this('translator')->setLocale($this('config')->get('app.locale'.($this('isAdmin') ? '_admin' : '')));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'init' => array('onInit', 16)
        );
    }
}
