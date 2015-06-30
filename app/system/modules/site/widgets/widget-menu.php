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

        if (ini_get('xdebug.max_nesting_level') < 1000) {
            ini_set('xdebug.max_nesting_level', 1000);
        }

        if (!$menu = $widget->get('menu')) {
            return '';
        }

        $nodes      = Node::where(['menu' => $menu, 'status' => 1])->orderBy('priority')->get();
        $root       = new Node();
        $activeId   = $app['node']->getId();
        $active     = null;
        $user       = $app['user'];
        $startLevel = (int) $widget->get('start_level', 1) - 1;
        $maxDepth   = $startLevel + ($widget->get('depth') ?: PHP_INT_MAX);

        foreach ($nodes as $node) {

            $parent = !$node->getParentId() ? $root : (isset($nodes[$node->getParentId()]) ? $nodes[$node->getParentId()] : null);

            if (!$parent || $parent->getDepth() >= $maxDepth || !$node->hasAccess($user)) {
                continue;
            }

            $node->setParent($parent);
            $parent->set('parent', true);

            if ($activeId === $node->getId()) {
                $active = $node;
            }
        }

        if ($active) {

            do {

                $active->set('active', true);

                if ($active->getDepth() == $startLevel) {
                    $root = $active;
                }

            } while ($active = $active->getParent());

        }


        if ($root->getDepth() != $startLevel) {
            return '';
        }

        $root->setParent();

        return $app['view']->render('menu', compact('widget', 'root'));
    }

];
