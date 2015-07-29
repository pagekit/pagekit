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

            if (!$type = $site->getType($node->getType())) {
                continue;
            }

            $type             = array_replace(['alias' => '', 'redirect' => '', 'controller' => ''], $type);
            $type['defaults'] = array_merge(isset($type['defaults']) ? $type['defaults'] : [], $node->get('defaults', []), ['_node' => $node->getId()]);
            $type['path']     = $node->getPath();

            $route = null;
            if ($node->get('alias')) {
                App::routes()->alias($node->getPath(), $node->getLink(), $type['defaults']);
            } elseif ($node->get('redirect')) {
                App::routes()->redirect($node->getPath(), $node->get('redirect'), $type['defaults']);
            } elseif ($type['controller']) {
                App::routes()->add($type);
            }

            if (!$frontpage && isset($type['frontpage']) && $type['frontpage']) {
                $frontpage = $node->getId();
            }

        }

        if ($frontpage && isset($nodes[$frontpage])) {
            App::routes()->alias('/', $nodes[$frontpage]->getLink());
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

                $node = Node::create();
                $node->setTitle($route['label']);
                $node->setSlug($this->slugify($route['label']));
                $node->setType($type);
                $node->setStatus(1);
                $node->setLink($route['name']);

                $node->save();
            }
        }
    }


    public function onNodeInit($event, $node)
    {
        if ('link' === $node->getType() && $node->get('redirect')) {
            $node->setLink($node->getPath());
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

    /**
     * Slugifys a string.
     *
     * @param  string $slug
     * @return string
     */
    protected function slugify($slug)
    {
        $slug = preg_replace('/\xE3\x80\x80/', ' ', $slug);
        $slug = str_replace('-', ' ', $slug);
        $slug = preg_replace('#[:\#\*"@+=;!><&\.%()\]\/\'\\\\|\[]#', "\x20", $slug);
        $slug = str_replace('?', '', $slug);
        $slug = trim(mb_strtolower($slug, 'UTF-8'));
        $slug = preg_replace('#\x20+#', '-', $slug);

        return $slug;
    }
}
