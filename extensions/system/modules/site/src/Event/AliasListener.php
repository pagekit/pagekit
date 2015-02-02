<?php

namespace Pagekit\Site\Event;

use Pagekit\Application as App;
use Pagekit\System\Event\TmplEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AliasListener implements EventSubscriberInterface
{
    /**
     * Registers alias type.
     */
    public function onSiteTypes(TypeEvent $event)
    {
        $event->register('alias', 'Alias', [
            'type'      => 'url',
            'tmpl.edit' => 'alias.edit'
        ]);
    }

    /**
     * Registers alias edit template.
     */
    public function onSystemTmpl(TmplEvent $event)
    {
        $event->register('alias.edit', 'extensions/system/modules/site/views/tmpl/site.alias.php');
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'site.types'  => 'onSiteTypes',
            'system.tmpl' => 'onSystemTmpl'
        ];
    }
}
