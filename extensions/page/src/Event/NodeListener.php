<?php

namespace Pagekit\Page\Event;

use Pagekit\Component\Database\Event\EntityEvent;
use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Page\Entity\Page;

class NodeListener extends EventSubscriber
{
    /**
     * @var Repository
     */
    protected $pages;

    public function onLoad(EntityEvent $event)
    {
        $node = $event->getEntity();
        if ('page' !== $node->getType()) {
            return;
        }

        $defaults = $node->get('defaults', []);
        if (isset($defaults['id']) && $page = $this->getPages()->find($defaults['id'])) {
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

        if (!isset($data['id']) || !$page = $this->getPages()->find($data['id'])) {
            $page = new Page;
        }

        $this->getPages()->save($page, $data);
        $data                   = $node->getData();
        $data['defaults']['id'] = $page->getId();
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
        if (isset($defaults['id']) && $page = $this->getPages()->find($defaults['id'])) {
            $this->getPages()->delete($page);
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


    /**
     * @return Repository
     */
    protected function getPages()
    {
        if (!$this->pages) {
            $this->pages = $this['db.em']->getRepository('Pagekit\Page\Entity\Page');
        }
        return $this->pages;
    }
}
