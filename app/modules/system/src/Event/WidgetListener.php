<?php

namespace Pagekit\System\Event;

use Pagekit\Application as App;
use Pagekit\Database\Event\EntityEvent;
use Pagekit\Widget\Event\WidgetCopyEvent;
use Pagekit\Widget\Event\WidgetEditEvent;
use Pagekit\Widget\Event\WidgetEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WidgetListener implements EventSubscriberInterface
{
    public function onSystemSite()
    {
        $settings = $this->getSettings();
        App::on('system.widget.postLoad', function(EntityEvent $event) use ($settings) {
            $widget = $event->getEntity();
            $widget->set('theme', isset($settings[$widget->getId()]) ? $settings[$widget->getId()] : []);
        });
    }

    public function onWidgetEdit(WidgetEditEvent $event)
    {
        if (!$theme = App::get('theme.site') or !$tmpl = $theme->config('widgets.view')) {
            return;
        }

        $settings = $this->getSettings();
        $event->addSettings(__('Theme'), function($widget) use ($tmpl, $theme, $settings) {
            return App::tmpl($tmpl, compact('widget', 'settings', 'theme'));
        });
    }

    public function onWidgetSave(WidgetEvent $event)
    {
        $settings = $this->getSettings();
        $settings[$event->getWidget()->getId()] = App::request()->get('_theme', []);
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
        return App::option($this->getOptionsName(), []);
    }

    protected function setSettings($settings)
    {
        App::option()->set($this->getOptionsName(), $settings, true);
    }

    protected function getOptionsName()
    {
        return App::get('theme.site')->name.':config.widgets';
    }
}
