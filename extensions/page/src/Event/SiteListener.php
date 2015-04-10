<?php

namespace Pagekit\Page\Event;

use Pagekit\Application as App;
use Pagekit\Database\Event\EntityEvent;
use Pagekit\Editor\Event\EditorLoadEvent;
use Pagekit\Page\Entity\Page;
use Pagekit\Site\Model\UrlType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SiteListener implements EventSubscriberInterface
{
    public function onSite($event)
    {
        App::trigger('editor.load', new EditorLoadEvent);
    }

    public function onSiteTypes($event, $site)
    {
        $site->registerType(new UrlType('page', __('Page'), '@page/id'));
    }

    public function onSiteSections($event, $site)
    {
        $site->registerSection('Content', function ($node) {

            App::view()->data('site', ['page' => $this->getPage($node)]);

            return App::view('page:views/admin/site/page.php', ['page' => $this->getPage($node)]);

        }, 'page');
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
    public static function getSubscribedEvents()
    {
        return [
            'view.site:views/admin/index.php' => 'onSite',
            'site.types'           => 'onSiteTypes',
            'site.sections'        => 'onSiteSections',
            'site.node.preSave'    => 'onSave',
            'site.node.postDelete' => 'onDelete'
        ];
    }
}
