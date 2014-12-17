<?php

namespace Pagekit\Tree\Event;

use Pagekit\Framework\Event\EventSubscriber;

class RouteListener extends EventSubscriber
{
    /**
     * Register node routes.
     */
    public function onSystemInit()
    {
        foreach ($this['option']->get('tree.nodes') ?: $this->cache() as $node) {

            if ($node['url']) {

                $this['aliases']->add($node['path'], $node['url']);

            } elseif ($node['mount'] and isset($this['mounts'][$node['mount']])) {

                $this['controllers']->mount($node['path'], $this['mounts'][$node['mount']]['controller'], "@{$node['mount']}/");

            }
        }
    }

    /**
     * @return array
     */
    public function cache()
    {
        $nodes = [];
        foreach ($this['db.em']->getRepository('Pagekit\Tree\Entity\Node')->query()->where(['status = ?'], [1])->get() as $node) {
            $nodes[] = ['path' => $node->getPath(), 'url' => $node->getUrl(), 'mount' => $node->getMount()];
        }

        $this['option']->set('tree.nodes', $nodes, true);

        return $nodes;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.init'          => ['onSystemInit', 10],
            'tree.node.postSave'   => 'cache',
            'tree.node.postDelete' => 'cache'
        ];
    }
}
