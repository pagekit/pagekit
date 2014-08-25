<?php

namespace Pagekit\Alpha;

use Pagekit\Framework\Application;
use Pagekit\Theme\Theme;

class AlphaTheme extends Theme
{
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

        $app->on('system.site', function() use ($app) {

            $app->on('view.layout', function($event) use ($app) {

                $event->setParameter('theme', $app['theme.site']);

            });

        });
    }

    public function getClasses()
    {
        if (null !== $this->classes) {
            return $this->classes;
        }

        $sidebars = array_replace_recursive([
            'sidebar-a' => ['width' => 12, 'alignment' => 'left'],
            'sidebar-b' => ['width' => 12, 'alignment' => 'right']
        ], $this->getParams('sidebars', []));
        $columns  = ['main' => ['width' => 60, 'alignment' => 'right']];

        $gcf = function($a, $b = 60) use(&$gcf) {
            return (int) ($b > 0 ? $gcf($b, $a % $b) : $a);
        };

        $fraction = function($nominator, $divider = 60) use(&$gcf) {
            return $nominator / ($factor = $gcf($nominator, $divider)) .'-'. $divider / $factor;
        };

        $sections = $this['view.sections'];
        foreach ($sidebars as $name => $sidebar) {
            if (!$sections->has($name)) {
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
