<?php

namespace Pagekit\Widget\Event;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;
use Pagekit\Widget\Entity\Widget;

class SiteListener implements EventSubscriberInterface
{
    /**
     * Registers node routes
     */
    public function onSite()
    {

        // register renderer
        foreach (App::module() as $module) {
            if (isset($module->renderer) && is_array($module->renderer)) {
                foreach ($module->renderer as $id => $renderer) {
                    App::view()->map('position.'.$id, $renderer);
                }
            }
        }

        // assign widgets
        $widgets = Widget::findAll();

        foreach (App::module('system/widget')->getPositions()->getAssigned() as $position => $ids) {

            foreach ($ids as $id) {

                if (!isset($widgets[$id]) or !$widget = $widgets[$id] or !$widget->hasAccess(App::user()) or ($nodes = $widget->getNodes() and !in_array(App::node()->getId(), $nodes))) {
                    continue;
                }

                //$widget->mergeSettings($this->getWidgetConfig($widget->getId()));

                App::view()->position($position, $widget);
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'site' => 'onSite'
        ];
    }
}
