<?php

namespace Pagekit\Theme\Event;

use Pagekit\Component\Database\Event\EntityEvent;
use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Widget\Event\WidgetCopyEvent;
use Pagekit\Widget\Event\WidgetEditEvent;
use Pagekit\Widget\Event\WidgetEvent;

class WidgetListener extends EventSubscriber
{
    public function onSystemSite()
    {
        $settings = $this->getSettings();
        $this['app']->on('system.widget.postLoad', function(EntityEvent $event) use ($settings) {
            $widget = $event->getEntity();
            $widget->set('theme', isset($settings[$widget->getId()]) ? $settings[$widget->getId()] : []);
        });
    }

    public function onWidgetEdit(WidgetEditEvent $event)
    {
        if (!$theme = $this['theme.site'] or !$tmpl = $theme->getConfig('parameters.widgets.view')) {
            return;
        }

        $view     = $this['view'];
        $settings = $this->getSettings();
        $event->addSettings(__('Theme'), function($widget) use ($view, $tmpl, $theme, $settings) {
            return $view->render($tmpl, compact('widget', 'settings', 'theme'));
        });
    }

    public function onWidgetSave(WidgetEvent $event)
    {
        $settings = $this->getSettings();
        $settings[$event->getWidget()->getId()] = $this['request']->get('_theme', []);
        $this->setSettings($settings);
    }

    public function onWidgetCopy(WidgetCopyEvent $event)
    {
        $settings = $this->getSettings();
        $settings[$event->getCopy()->getId()] = $settings[$event->getWidget()->getId()];
        $this->setSettings($settings);
    }

    public function onWidgetDelete(EntityEvent $event)
    {
        $settings = $this->getSettings();
        unset($settings[$event->getEntity()->getId()]);
        $this->setSettings($settings);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.site'              => 'onSystemSite',
            'system.widget.edit'       => 'onWidgetEdit',
            'system.widget.save'       => 'onWidgetSave',
            'system.widget.copy'       => 'onWidgetCopy',
            'system.widget.postDelete' => 'onWidgetDelete'
        ];
    }

    protected function getSettings()
    {
        return $this['option']->get($this->getOptionsName(), []);
    }

    protected function setSettings($settings)
    {
        $this['option']->set($this->getOptionsName(), $settings, true);
    }

    protected function getOptionsName()
    {
        return $this['theme.site']->getName().':settings.widgets';
    }
}
