<?php

namespace Pagekit\Site\Event;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;
use Pagekit\Site\Model\Node;

class NodesListener implements EventSubscriberInterface
{
    /**
     * Registers node routes
     */
    public function onRequest()
    {
        $site      = App::module('system/site');
        $frontpage = $site->config('frontpage');
        $nodes     = Node::where(['status' => 1])->get();

        foreach ($nodes as $node) {

            if (!$type = $site->getType($node->type)) {
                continue;
            }

            $type             = array_replace(['alias' => '', 'redirect' => '', 'controller' => ''], $type);
            $type['defaults'] = array_merge(isset($type['defaults']) ? $type['defaults'] : [], $node->get('defaults', []), ['_node' => $node->id]);
            $type['path']     = $node->path;

            $route = null;
            if ($node->get('alias')) {
                App::routes()->alias($node->path, $node->link, $type['defaults']);
            } elseif ($node->get('redirect')) {
                App::routes()->redirect($node->path, $node->get('redirect'), $type['defaults']);
            } elseif ($type['controller']) {
                App::routes()->add($type);
            }

            if (!$frontpage && isset($type['frontpage']) && $type['frontpage']) {
                $frontpage = $node->id;
            }

        }

        if ($frontpage && isset($nodes[$frontpage])) {
            App::routes()->alias('/', $nodes[$frontpage]->link);
        } else {
            App::routes()->get('/', function () {
                return __('No Frontpage assigned.');
            });
        }
    }

    /**
     * Adds protected node types.
     */
    public function onEnable($event, $module)
    {
        foreach ((array) $module->get('nodes') as $type => $route) {
            if (isset($route['protected']) and $route['protected'] and !Node::where(['type = ?'], [$type])->first()) {
                Node::create([
                    'title' => $route['label'],
                    'slug' => App::filter($route['label'], 'slugify'),
                    'type' => $type,
                    'status' => 1,
                    'link' => $route['name']
                ])->save();
            }
        }
    }

    public function onNodeInit($event, $node)
    {
        if ('link' === $node->type && $node->get('redirect')) {
            $node->link = $node->path;
        }
    }

    public function onRoleDelete($event, $role)
    {
        Node::removeRole($role);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'request' => ['onRequest', 110],
            'enable' => 'onEnable',
            'model.node.init' => 'onNodeInit',
            'model.role.deleted' => 'onRoleDelete'
        ];
    }
}
