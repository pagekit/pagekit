<?php

namespace Pagekit\Widget;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\Util\Arr;
use Pagekit\Widget\Entity\Widget;
use Pagekit\Widget\Model\TypeInterface;

class WidgetModule extends Module
{
    protected $types     = [];
    protected $positions = [];

    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app->on('view.site:views/admin/index', function ($event, $view) use ($app) {

            if (!$app['user']->hasAccess('system: manage widgets')) {
                return;
            }

            $view->script('widgets', 'widget:app/bundle/site.js', ['site', 'uikit-form-select']);

            $view->data('$widgets', [

                'positions' => array_values($this->getPositions()),
                'types'     => array_values($this->getTypes())

            ]);

        });

        $app->on('app.site', function ($event, $request) use ($app) {

            // register renderer
            foreach ($app['module'] as $module) {

                if (!isset($module->renderer) || !is_array($module->renderer)) {
                    continue;
                }

                foreach ($module->renderer as $id => $renderer) {
                    $app['view']->map('position.'.$id, $renderer);
                }
            }

            $app['view']->map('position.default', 'widget:views/widgets.php');

            // assign widgets
            $active    = (array) $request->attributes->get('_node');
            $user      = $app['user'];
            $positions = $app['view']->position();
            $widgets   = Widget::findAll();

            foreach ($this->config('widget.positions') as $position => $ids) {

                if (!$this->hasPosition($position)) {
                    continue;
                }

                foreach ($ids as $id) {

                    if (!isset($widgets[$id]) or !$widget = $widgets[$id] or !$widget->hasAccess($user) or ($nodes = $widget->getNodes() and !array_intersect($nodes, $active))) {
                        continue;
                    }

                    $widget->mergeSettings($this->getWidgetConfig($widget->getId()));

                    $positions($position, $widget);
                }
            }

        });

        $app->on('app.request', function () use ($app) {
            $app['scripts']->register('widgets', 'widget:app/bundle/widgets.js', 'vue');
            // $this->config->merge(['widget' => ['defaults' => $app['theme.site']->config('widget.defaults', [])]]);
        });

    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        $widgets = Widget::findAll();

        foreach ($this->config('widget.positions') as $position => $assigned) {
            foreach ($assigned as $id) {
                if (isset($widgets[$id])) {
                    $widgets[$id]->position = $position;
                }
            }
        }

        return $widgets;
    }

    /**
     * @param  string $name
     * @return bool
     */
    public function hasPosition($name)
    {
        $positions = $this->getPositions();

        return isset($positions[$name]);
    }

    /**
     * @return array
     */
    public function getPositions()
    {
        if (!$this->positions) {

            foreach (App::module() as $module) {

                if (!isset($module->positions) || !is_array($module->positions)) {
                    continue;
                }

                foreach ($module->positions as $id => $position) {
                    list($name, $description) = array_merge((array) $position, ['']);
                    $this->registerPosition($id, $name, $description);
                }
            }

            App::trigger('widget.positions', [$this]);
        }

        return $this->positions;
    }

    /**
     * Registers a position.
     *
     * @param string $id
     * @param string $name
     * @param string $description
     */
    public function registerPosition($id, $name, $description = '')
    {
        $this->positions[$id] = compact('id', 'name', 'description');
    }

    /**
     * @param  string $type
     * @return TypeInterface
     */
    public function getType($type)
    {
        $types = $this->getTypes();

        return isset($types[$type]) ? $types[$type] : null;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        if (!$this->types) {

            $this->registerType(new TextWidget());
            App::trigger('widget.types', [$this]);

        }

        return $this->types;
    }

    /**
     * Registers a type.
     *
     * @param TypeInterface $type
     */
    public function registerType(TypeInterface $type)
    {
        $this->types[$type->getId()] = $type;
    }

    /**
     * Gets the widget config.
     *
     * @param  int $id
     * @return array
     */
    public function getWidgetConfig($id = 0)
    {
        return Arr::merge($this->config('widget.defaults'), $this->config("widget.config.$id", []), true);
    }
}
