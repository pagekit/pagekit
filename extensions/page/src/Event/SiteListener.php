<?php

namespace Pagekit\Page\Event;

use Pagekit\Application as App;
use Pagekit\Database\Event\EntityEvent;
use Pagekit\Editor\Event\EditorLoadEvent;
use Pagekit\Event\EventSubscriberInterface;
use Pagekit\Page\Entity\Page;
use Pagekit\Site\Model\UrlType;

class SiteListener implements EventSubscriberInterface
{
    public function onSite($event, $view)
    {
        $view->script('page-site', 'page:app/bundle/site.js', 'site');
        App::trigger(new EditorLoadEvent('editor.load'));
    }

    public function onSiteTypes($event, $site)
    {
        $site->registerType(new UrlType('page', __('Page'), '@page/id'));
    }

    public function onSave(EntityEvent $event)
    {
        $node = $event->getEntity();
        $data = App::request()->get('page');

        if ('page' !== $node->getType() or $data === null) {
            return;
        }

        $page = $this->getPage($node);
        $page->save($data);

        $node->set('variables', ['id' => $page->getId()]);
    }

    public function onDelete(EntityEvent $event)
    {
        $node = $event->getEntity();

        if ('page' !== $node->getType()) {
            return;
        }

        $page = $this->getPage($node);

        if ($page->getId()) {
            $page->delete();
        }
    }

    protected function getPage($node)
    {
        $variables = $node->get('variables', []);

        if (!isset($variables['id']) or !$page = Page::find($variables['id'])) {
            $page = new Page();
        }

        return $page;
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'view.site:views/admin/index' => 'onSite',
            'site.types'                  => 'onSiteTypes',
            'site.node.preSave'           => 'onSave',
            'site.node.postDelete'        => 'onDelete'
        ];
    }
}
