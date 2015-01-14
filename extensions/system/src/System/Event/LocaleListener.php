<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Application as App;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocaleListener implements  EventSubscriberInterface
{
    /**
     * Sets the systems locale
     */
    public function onSystemInit()
    {
        App::translator()->setLocale(App::config()->get('app.locale'.(App::isAdmin() ? '_admin' : '')));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.init' => ['onSystemInit', 10]
        ];
    }
}
