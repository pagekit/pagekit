<?php

namespace Pagekit\Menu\Widget;

use Pagekit\Widget\Model\Type;
use Pagekit\Widget\Model\WidgetInterface;

class MenuWidget extends Type
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'widget.menu';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Menu');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(WidgetInterface $widget = null)
    {
        return __('Menu Widget');
    }

    /**
     * {@inheritdoc}
     */
    public function render(WidgetInterface $widget, $options = [])
    {
        if (ini_get('xdebug.max_nesting_level') < 1000) {
            ini_set('xdebug.max_nesting_level', 1000);
        }

        $layout = isset($options['layout']) ? $options['layout'] : 'extension://system/views/widgets/menu/nav.razr';

        $root = $this['menus']->getTree($widget->get('menu', 0), [
            'access' => true,
            'active' => true,
            'status' => 1,
        ]);

        $startLevel = (int) $widget->get('start_level', 1) - 1;
        $maxDepth   = $startLevel + ($widget->get('depth') ?: PHP_INT_MAX);

        foreach (new \RecursiveIteratorIterator($root, \RecursiveIteratorIterator::CHILD_FIRST) as $node) {

            $parent = $node->getParent();
            $parent->setAttribute('parent', true);

            $depth = $node->getDepth();

            if ($depth > $maxDepth) {
                $node->setParent();
            }

            if ($node->getAttribute('active')) {

                $parent->setAttribute('active', true);

                if ($depth == $startLevel) {
                    $root = $node;
                }
            }
        }

        if ($root->getDepth() != $startLevel) {
            return '';
        }

        $root->setParent();

        return $this['view']->render($layout, compact('widget', 'options', 'root'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return $this['view']->render('extension://system/views/widgets/menu/edit.razr', compact('widget'));
    }
}
