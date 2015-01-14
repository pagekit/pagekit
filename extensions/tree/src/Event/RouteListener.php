<?php

namespace Pagekit\Tree\Event;

use Pagekit\Application as App;
use Pagekit\Tree\Entity\Node;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RouteListener implements EventSubscriberInterface
{
    const CACHE_KEY = 'tree.nodes';

    /**
     * Register node routes.
     */
    public function onSystemInit()
    {
        foreach ($this->getNodes() as $path => $node) {

            if ($node['controllers']) {

                App::controllers()->mount($path, $node['controllers'], "@{$node['id']}/", $node['defaults']);

            } elseif ($node['url']) {

                App::aliases()->add($path, $node['url'], $node['defaults']);

            }
        }
    }

    /**
     * Clears the url aliases cache.
     */
    public function clearCache()
    {
        App::get('cache.phpfile')->delete(self::CACHE_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.init'          => ['onSystemInit', 10],
            'tree.node.postSave'   => 'clearCache',
            'tree.node.postDelete' => 'clearCache'
        ];
    }

    /**
     * @return array
     */
    protected function getNodes()
    {
        if (!$nodes = App::get('cache.phpfile')->fetch(self::CACHE_KEY) ?: []) {

            $nodes = [];
            $types = App::get('tree.types');
            foreach (Node::query()->where(['status = ?'], [1])->get() as $node) {

                if (!$type = $types[$node->getType()]) {
                    continue;
                }

                $nodes[$node->getPath()] = [
                    'id'          => $type['id'],
                    'url'         => $node->get('url', ''),
                    'controllers' => isset($type['controllers']) ? $type['controllers'] : '',
                    'defaults'    => array_merge_recursive(isset($type['defaults']) ? $type['defaults'] : [], $node->get('defaults', []))
                ];
            }

            App::get('cache.phpfile')->save(self::CACHE_KEY, $nodes);
        }

        return $nodes;
    }
}
