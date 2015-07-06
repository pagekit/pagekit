<?php

use Pagekit\Site\Model\Node;

return [

    'name' => 'system/widget-menu',

    'label' => 'Menu',

    'type' => 'widget',

    'views' => [
        'menu' => 'system/site:views/widget-menu.php'
    ],

    'events' => [

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('widget-menu', 'system/site:app/bundle/widget-menu.js', '~widgets');
        }

    ],

    'render' => function ($widget) use ($app) {

        if (!$menu = $widget->get('menu')) {
            return '';
        }

        $user       = $app['user'];
        $startLevel = (int) $widget->get('start_level', 1);
        $maxDepth   = $startLevel + ($widget->get('depth') ?: PHP_INT_MAX);

        $path       = $app['node']->getPath();
        $segments   = explode('/', $path);
        $rootPath   = count($segments) > $startLevel ? implode('/', array_slice($segments, 0, $startLevel + 1)) : '';

        $nodes      = Node::where(['menu' => $menu, 'status' => 1])->orderBy('priority')->get();
        $nodes[0]   = new Node();
        $nodes[0]->setParentId(null);

        foreach ($nodes as $node) {

            $depth  = substr_count($node->getPath(), '/');
            $parent = isset($nodes[$node->getParentId()]) ? $nodes[$node->getParentId()] : null;

            $node->set('active', !$node->getPath() || 0 === strpos($path, $node->getPath()));

            if ($depth >= $maxDepth
                || !$node->hasAccess($user)
                || !($widget->get('mode') == 'all'
                    || $node->get('active')
                    || $rootPath && 0 === strpos($node->getPath(), $rootPath)
                    || $depth == $startLevel)
            ) {
                continue;
            }

            $node->setParent($parent);

            if ($node->get('active') && $depth == $startLevel - 1) {
                $root = $node;
            }

        }

        if (!isset($root)) {
            return '';
        }

        $root->setParent();

        return $app['view']->render('menu', compact('widget', 'root'));
    }

];
