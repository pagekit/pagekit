<?php

namespace Pagekit\Site\Event;

use Pagekit\Application as App;
use Pagekit\Site\Entity\Node;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RouteListener implements EventSubscriberInterface
{
    /**
     * Registers node routes.
     */
    public function onKernelRequest()
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
     * @return array
     */
    protected function getNodes()
    {
        $nodes = [];
        $types = App::module('site')->getTypes();

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

        return $nodes;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => ['onKernelRequest', 35]
        ];
    }
}
