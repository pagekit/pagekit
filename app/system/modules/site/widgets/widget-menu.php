<?php

use Pagekit\Site\Entity\Node;

return [

    'name' => 'system/widget-menu',

    'type' => 'widget',

    'main' => function ($app) {

    },

    'views' => [
        'menu' => 'system/site:views/widget-menu.php'
    ],

    'events' => [

        'view.layout' => function () use ($app) {
            $app['scripts']->register('widget-menu', 'system/site:app/bundle/widget-menu.js', '~widgets');
        }

    ],

    'render' => function ($widget) use ($app) {

        if (!$menu = $widget->get('menu')) {
            return '';
        }

        $nodes      = Node::where(['menu' => $menu, 'status' => 1])->orderBy('priority')->get();
        $nodes[0]   = new Node();
        $root       = $nodes[0];
        $path       = $app['node']->getPath();
        $user       = $app['user'];
        $startLevel = (int) $widget->get('start_level', 1) - 1;
        $maxDepth   = $startLevel + ($widget->get('depth') ?: PHP_INT_MAX);

        $root->setParentId(null);
        foreach ($nodes as $node) {

            $depth  = substr_count($node->getPath(), '/');
            $parent = isset($nodes[$node->getParentId()]) ? $nodes[$node->getParentId()] : null;

            $node->set('active', 0 === strpos($path, $node->getPath()));

            if (!$parent
                || $depth > $maxDepth
                || !$node->hasAccess($user)
                || (!$node->get('active')
                    && $widget->get('mode') == 'active'
                    && 0 !== strpos($node->getPath(), $path))
            ) {
                continue;
            }

            $node->setParent($parent);
            $parent->set('parent', true);

            if ($node->get('active') && $depth == $startLevel) {
                $root = $node;
            }

        }

        if ($root->getDepth() != $startLevel) {
            return '';
        }

        $root->setParent();

        return $app['view']->render('menu', compact('widget', 'root'));
    }

];
