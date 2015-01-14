<?php

namespace Pagekit\Page\Event;

use Pagekit\Database\Event\EntityEvent;
use Pagekit\Page\Entity\Page;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NodeListener implements EventSubscriberInterface
{
    public function onLoad(EntityEvent $event)
    {
        $node = $event->getEntity();
        if ('page' !== $node->getType()) {
            return;
        }

        $defaults = $node->get('defaults', []);
        if (isset($defaults['id']) && $page = Page::find($defaults['id'])) {
            $node->set('page', $page);
        }
    }

    public function onSave(EntityEvent $event)
    {
        $node = $event->getEntity();
        if ('page' !== $node->getType()) {
            return;
        }

        $data = $node->get('page');

        if (!isset($data['id']) || !$page = Page::find($data['id'])) {
            $page = new Page;
        }

        $page->save($data);

        $data                   = $node->getData();
        $data['defaults']['id'] = $page->getId();
        $data['url']            = '@page/id?id='.$page->getId();
        unset($data['page']);
        $node->setData($data);
    }

    public function onDelete(EntityEvent $event)
    {
        $node = $event->getEntity();

        if ('page' !== $node->getType()) {
            return;
        }

        $defaults = $node->get('defaults', []);

        if (isset($defaults['id']) && $page = Page::find($defaults['id'])) {
            $page->delete();
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'tree.node.postLoad'   => 'onLoad',
            'tree.node.preSave'    => 'onSave',
            'tree.node.postSave'   => 'onLoad',
            'tree.node.postDelete' => 'onDelete'
        ];
    }
}
