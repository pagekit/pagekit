<?php

namespace Pagekit\Site\Event;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;
use Pagekit\Site\Model\Node;
use Pagekit\Site\Model\Page;

class PageListener implements EventSubscriberInterface
{
    public function onNodeSave($event, $node)
    {
        if ('page' !== $node->type or null === $data = App::request()->get('page')) {
            return;
        }

        $page = $this->getPage($node);
        $page->save($data);

        $node->set('defaults', ['id' => $page->id]);
        $node->link = '@page/'.$page->id;
    }

    public function onNodeDeleted($event, $node)
    {
        if ('page' !== $node->type) {
            return;
        }

        $page = $this->getPage($node);

        if ($page->id) {
            $page->delete();
        }
    }

    public function onRouteConfigure($event, $route, $routes)
    {
        if ($route->getName() === '@page') {
            $routes->remove('@page');
            $route->setName('@page/'.$route->getDefault('id'));
            $routes->add($route->getName(), $route);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'model.node.saving' => 'onNodeSave',
            'model.node.deleted' => 'onNodeDeleted',
            'route.configure' => 'onRouteConfigure'
        ];
    }

    /**
     * Find page entity by node.
     *
     * @param  Node $node
     * @return Page
     */
    protected function getPage(Node $node)
    {
        if (!$id = $node->get('defaults.id', 0) or !$page = Page::find($id)) {
            $page = Page::create();
        }

        return $page;
    }
}
