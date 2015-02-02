<?php

namespace Pagekit\Site\Event;

use Pagekit\Application as App;
use Pagekit\Site\Entity\Node;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RouteListener implements EventSubscriberInterface
{
    const CACHE_KEY = 'site.nodes';

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
            'system.init'            => ['onSystemInit', 10],
            'system.node.postSave'   => 'clearCache',
            'system.node.postDelete' => 'clearCache'
        ];
    }

    /**
     * @return array
     */
    protected function getNodes()
    {
        if (!$nodes = App::get('cache.phpfile')->fetch(self::CACHE_KEY) ?: []) {

            $nodes = [];
            $types = App::get('site.types');
            foreach (Node::where(['status = ?'], [1])->get() as $node) {

                if (!isset($types[$node->getType()])) {
                    continue;
                }

                $type = $types[$node->getType()];

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
