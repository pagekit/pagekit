<?php

namespace Pagekit\Alpha\Event;

use Pagekit\Component\Database\Event\EntityEvent;
use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Theme\Theme;
use Pagekit\Widget\Event\WidgetCopyEvent;
use Pagekit\Widget\Event\WidgetEditEvent;
use Pagekit\Widget\Event\WidgetEvent;

class WidgetListener extends EventSubscriber
{
    /**
     * @var Theme
     */
    protected $theme;

    public function onWidgetEdit(WidgetEditEvent $event)
    {
        $view = $this('view');
        $settings = $this->getSettings();
        $event->addSettings(__('Theme: Alpha'), function($widget) use ($view, $settings) {
            return $view->render('theme://alpha/views/admin/widgets/edit.razr.php', compact('widget', 'settings'));
        });
    }

    public function onWidgetSave(WidgetEvent $event)
    {
        $settings = $this->getSettings();
        $settings[$event->getWidget()->getId()] = $this('request')->get('theme_alpha', array());
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
        return array(
            'system.widget.edit'       => 'onWidgetEdit',
            'system.widget.save'       => 'onWidgetSave',
            'system.widget.copy'       => 'onWidgetCopy',
            'system.widget.postDelete' => 'onWidgetDelete'
        );
    }

    protected function getSettings()
    {
        $config = $this('option')->get('alpha:config', array());

        return isset($config['widgets']) ? $config['widgets'] : array();
    }

    protected function setSettings($settings)
    {
        $config = $this('option')->get('alpha:config', array());

        $config['widgets'] = $settings;

        $this('option')->set('alpha:config', $config);
    }

    /**
     * @return Repository
     */
    protected function getTheme()
    {
        if (!$this->theme) {
            $this->theme = $this('themes')->get('alpha');
        }

        return $this->theme;
    }
}