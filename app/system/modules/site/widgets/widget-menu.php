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

        if (ini_get('xdebug.max_nesting_level') < 1000) {
            ini_set('xdebug.max_nesting_level', 1000);
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

            $parent = isset($nodes[$node->getParentId()]) ? $nodes[$node->getParentId()] : null;

            if (!$parent || $parent->getDepth() >= $maxDepth || !$node->hasAccess($user)) {
                continue;
            }

            $node->setParent($parent);
            $parent->set('parent', true);

            if (0 === strpos($path, $node->getPath())) {
                $node->set('active', true);
                if ($node->getDepth() == $startLevel) {
                    $root = $node;
                }
            }
        }

        if ($root->getDepth() != $startLevel) {
            return '';
        }

        $root->setParent();

        return $app['view']->render('menu', compact('widget', 'root'));
    }

];
