<?php

namespace Pagekit\Page\Event;

use Pagekit\Component\Database\Event\EntityEvent;
use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Page\Entity\Page;

class NodeListener extends EventSubscriber
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

        Page::save($page, $data);
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
            Page::delete($page);
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
