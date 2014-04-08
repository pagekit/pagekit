<?php

namespace Pagekit\Alpha;

use Pagekit\Alpha\Event\WidgetListener;
use Pagekit\Framework\Application;
use Pagekit\Theme\Theme;

class AlphaTheme extends Theme
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $classes;

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        parent::boot($app);

        $app['events']->addSubscriber(new WidgetListener);

        $this->app = $app;
        $this->config += $app['option']->get('alpha:config', array());

        $app->on('admin.init', function() use ($app) {

            $app['router']->addController('Pagekit\Alpha\Controller\SettingsController', array('name' => 'alpha'));

        });

        $app->on('site.init', function() use ($app) {

            $app->on('system.position.renderer', function($event) use ($app) {

                $event->register('blank',     'theme://alpha/views/renderer/position.blank.razr.php');
                $event->register('grid',      'theme://alpha/views/renderer/position.grid.php');
                $event->register('navbar',    'theme://alpha/views/renderer/position.navbar.razr.php');
                $event->register('offcanvas', 'theme://alpha/views/renderer/position.offcanvas.razr.php');
                $event->register('panel',     'theme://alpha/views/renderer/position.panel.razr.php');

            });

            $app->on('view.layout', function($event) use ($app) {

                $event->setParameter('position', $app['positions']);
                $event->setParameter('theme', $app['theme.site']);

            });

        });
    }

    public function getClasses()
    {
        if (null !== $this->classes) {
            return $this->classes;
        }

        $sidebars = array_replace_recursive(array(
            'sidebar-a' => array('width' => 12, 'alignment' => 'left'),
            'sidebar-b' => array('width' => 12, 'alignment' => 'right')
        ), $this->getConfig('sidebars', array()));
        $columns  = array('main' => array('width' => 60, 'alignment' => 'right'));

        $gcf = function($a, $b = 60) use(&$gcf) {
            return (int) ($b > 0 ? $gcf($b, $a % $b) : $a);
        };

        $fraction = function($nominator, $divider = 60) use(&$gcf) {
            return $nominator / ($factor = $gcf($nominator, $divider)) .'-'. $divider / $factor;
        };

        foreach ($sidebars as $name => $sidebar) {
            if (!$this->app['positions']->exists($name)) {
                unset($sidebars[$name]);
                continue;
            }

            $columns['main']['width'] -= @$sidebar['width'];
        }

        $columns += $sidebars;
        foreach ($columns as $name => &$column) {

            $column['width']     = isset($column['width']) ? $column['width'] : 0;
            $column['alignment'] = isset($column['alignment']) ? $column['alignment'] : 'left';

            $shift = 0;
            foreach (($column['alignment'] == 'left' ? $columns : array_reverse($columns, true)) as $n => $col) {
                if ($name == $n) break;
                if (@$col['alignment'] != $column['alignment']) {
                    $shift += @$col['width'];
                }
            }
            $column['class'] = sprintf('tm-%s uk-width-medium-%s%s', $name, $fraction($column['width']), $shift ? ' uk-'.($column['alignment'] == 'left' ? 'pull' : 'push').'-'.$fraction($shift) : '');
        }

        $this->classes = compact('columns');
        return $this->classes;
    }
}
