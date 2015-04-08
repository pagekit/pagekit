<?php

namespace Pagekit\Page\Event;

use Pagekit\Application as App;
use Pagekit\Database\Event\EntityEvent;
use Pagekit\Page\Entity\Page;
use Pagekit\Site\Model\UrlType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SiteListener implements EventSubscriberInterface
{
    public function onSiteTypes($event, $site)
    {
        $site->registerType(new UrlType('page', __('Page'), '@page/id'));
    }

    public function onSiteSections($event, $site)
    {
        $site->registerSection('Content', function ($node) {

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

        $defaults       = $node->get('variables', []);
        $defaults['id'] = $page->getId();
        $node->set('variables', $defaults);
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
        $defaults = $node->get('variables', []);

        if (!isset($defaults['id']) or !$page = Page::find($defaults['id'])) {
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
            'site.types'           => 'onSiteTypes',
            'site.sections'        => 'onSiteSections',
            'site.node.preSave'    => 'onSave',
            'site.node.postDelete' => 'onDelete'
        ];
    }
}
