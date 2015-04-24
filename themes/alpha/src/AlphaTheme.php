<?php

namespace Pagekit\Alpha;

use Pagekit\Application as App;
use Pagekit\System\Theme;
use Pagekit\Util\Arr;

class AlphaTheme extends Theme
{
    /**
     * @var array
     */
    protected $classes;

    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app->on('app.site', function () use ($app) {
            $app['view']->on('layout', function ($event, $view) use ($app) {
                $view->setName($this->getLayout());
                $view->setParameter('theme', $app['theme.site']);
            });
        });

        $app->on('widget.sections', function ($event, $widgets) {
            $widgets->registerSection('Theme', 'alpha:views/admin/widgets/edit.php');
        });
    }

    public function getClasses($key = null)
    {
        if (null === $this->classes) {
            $this->buildClasses();
        }

        if (null === $key) {
            return $this->classes;
        }

        return Arr::get($this->classes, $key);
    }

    protected function buildClasses()
    {
        $sidebars = array_replace_recursive([
            'sidebar-a' => ['width' => 12, 'alignment' => 'left'],
            'sidebar-b' => ['width' => 12, 'alignment' => 'right']
        ], $this->config('sidebars', []));
        $columns  = ['main' => ['width' => 60, 'alignment' => 'right']];

        $gcf = function ($a, $b = 60) use(&$gcf) {
            return (int) ($b > 0 ? $gcf($b, $a % $b) : $a);
        };

        $fraction = function ($nominator, $divider = 60) use(&$gcf) {
            return $nominator / ($factor = $gcf($nominator, $divider)) .'-'. $divider / $factor;
        };

        $sections = App::view()->section();
        foreach ($sidebars as $name => $sidebar) {
            if (!$sections->exists($name)) {
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
    }
}
