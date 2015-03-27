<?php

namespace Pagekit\Menu\Widget;

use Pagekit\Application as App;
use Pagekit\Widget\Model\TypeInterface;
use Pagekit\Widget\Model\WidgetInterface;

class MenuWidget implements TypeInterface
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
        // -TODO- fix menu render
        return 'Menu';

        if (ini_get('xdebug.max_nesting_level') < 1000) {
            ini_set('xdebug.max_nesting_level', 1000);
        }

        $layout = isset($options['layout']) ? $options['layout'] : 'app/modules/menu/views/widgets/menu/nav.razr';

        $root = App::menus()->getTree($widget->get('menu', 0), [
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

        return App::tmpl($layout, compact('widget', 'options', 'root'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return App::tmpl('app/modules/menu/views/widgets/menu/edit.razr', compact('widget'));
    }
}
