<?php

namespace Pagekit\Tree\Event;

use Pagekit\Framework\Event\EventSubscriber;

class RouteListener extends EventSubscriber
{
    const CACHE_KEY = 'tree.nodes';

    /**
     * Register node routes.
     */
    public function onSystemInit()
    {
        foreach ($this->getNodes() as $path => $node) {

            if (is_string($node)) {

                $this['aliases']->add($path, $node);

            } else {

                $this['controllers']->mount($path, $node['controllers'], "@{$node['id']}/", $node['defaults']);

            }
        }
    }

    /**
     * Clears the url aliases cache.
     */
    public function clearCache()
    {
        $this['cache.phpfile']->delete(self::CACHE_KEY);
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
        if (!$nodes = $this['cache.phpfile']->fetch(self::CACHE_KEY) ?: []) {

            $nodes = [];
            $types = $this['tree.types'];
            foreach ($this['db.em']->getRepository('Pagekit\Tree\Entity\Node')->query()->where(['status = ?'], [1])->get() as $node) {

                if (!isset($types[$node->getType()])) {
                    continue;
                }

                $type = $types[$node->getType()];

                $type['defaults'] = array_merge_recursive(isset($type['defaults']) ? $type['defaults'] : [], $node->get('defaults', []));

                $nodes[$node->getPath()] = in_array($type['type'], ['node', 'mount']) ? $type : $node->get('url', '');
            }

            $this['cache.phpfile']->save(self::CACHE_KEY, $nodes);
        }

        return $nodes;
    }
}
