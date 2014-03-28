<?php

namespace Pagekit\Alpha;

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

        $this->app = $app;
        $this->config += $app['option']->get('alpha:config', array());
    }

    public function getClasses()
    {
        if (null !== $this->classes) {
            return $this->classes;
        }

        $sidebars = $this->getConfig('sidebars', array());
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
            $column['class'] = sprintf('tm-%s uk-width-medium-%s%s %s %s', $name, $fraction($column['width']), $shift ? ' uk-'.($column['alignment'] == 'left' ? 'pull' : 'push').'-'.$fraction($shift) : '', isset($column['style']) ? $column['style'] : '', isset($column['divider']) ? 'uk-grid-divider' : '');
        }

        $this->classes = compact('columns');
        return $this->classes;
    }
}
