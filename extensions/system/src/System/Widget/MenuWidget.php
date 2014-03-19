<?php

namespace Pagekit\System\Widget;

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
        $layout = isset($options['layout']) ? $options['layout'] : "system/widgets/menu/style.{$widget->get('style', 'list')}.razr.php";

        $root = $this('menus')->getTree($widget->get('menu', 0), array(
            'access' => true,
            'active' => true,
            'status' => 1,
        ));

        foreach (new \RecursiveIteratorIterator($root, \RecursiveIteratorIterator::CHILD_FIRST) as $node) {

            if ($node->getAttribute('active')) {

                if ($item = $node->getParent()->getItem()) {
                    $item->setAttribute('active', true);
                }

                if ($node->getDepth() < $widget->get('start_level', 1)) {
                    $root = $node;
                }
            }
        }

        if ($root->getDepth() != $widget->get('start_level', 1) - 1) {
            return '';
        }

        return $this('view')->render($layout, compact('widget', 'options', 'root'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        $menus  = $this('menus')->getMenuRepository()->findAll();
        $styles = array('list', 'sidebar');

        return $this('view')->render('system/widgets/menu/edit.razr.php', compact('widget', 'menus', 'styles'));
    }
}
