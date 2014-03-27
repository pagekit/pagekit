<?php

namespace Pagekit\Menu\Widget;

use Pagekit\Framework\ApplicationAware;
use Pagekit\Widget\Model\TypeInterface;
use Pagekit\Widget\Model\WidgetInterface;

class MenuWidget extends ApplicationAware implements TypeInterface
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
    public function getDescription()
    {
        return __('Menu Widget');
    }

    /**
     * {@inheritdoc}
     */
    public function getInfo(WidgetInterface $widget)
    {
        return __('Menu Widget');
    }

    /**
     * {@inheritdoc}
     */
    public function render(WidgetInterface $widget, $options = array())
    {
        $layout = isset($options['layout']) ? $options['layout'] : "system/widgets/menu/style.nav.razr.php";

        $root = $this('menus')->getTree($widget->get('menu', 0), array(
            'access' => true,
            'active' => true,
            'status' => 1,
        ));

        $startLevel = (int) $widget->get('start_level', 1) - 1;
        $maxDepth   = $widget->get('depth');

        foreach (new \RecursiveIteratorIterator($root, \RecursiveIteratorIterator::CHILD_FIRST) as $node) {

            $parent = $node->getParent();
            $parent->setAttribute('parent', true);

            if ($maxDepth && $node->getDepth() > $startLevel + $maxDepth) {
                $node->setParent();
            }

            if ($node->getAttribute('active')) {

                $parent->setAttribute('active', true);

                if ($node->getDepth() == $startLevel) {
                    $root = $node;
                }
            }
        }

        if ($root->getDepth() != $startLevel) {
            return '';
        }

        $root->setParent();

        return $this('view')->render($layout, compact('widget', 'options', 'root'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        $menus  = $this('menus')->getMenuRepository()->findAll();

        return $this('view')->render('system/widgets/menu/edit.razr.php', compact('widget', 'menus'));
    }
}
