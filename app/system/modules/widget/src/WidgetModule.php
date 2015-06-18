<?php

namespace Pagekit\Widget;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\Module\Factory\ModuleFactory;
use Pagekit\Util\Arr;
use Pagekit\Widget\Entity\Widget;
use Pagekit\Widget\Model\TypeInterface;

class WidgetModule extends Module
{
    protected $types = [];
    protected $registered = [];
    protected $positions;

    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $this->positions = new PositionManager($this->config('widget.positions'));

        $app['scripts']->register('widgets', 'widget:app/bundle/widgets.js', 'vue');
        // $this->config->merge(['widget' => ['defaults' => $app['theme.site']->config('widget.defaults', [])]]);

        $app['module']->addFactory('widget', function ($module) use ($app) {

            $class = is_string($module['main']) ? $module['main'] : 'Pagekit\Widget\Model\Type';

            $module = new $class($module);
            $module->main($app);

            $this->registerType($module);

            return $module;
        });

        $app->on('system.widget.postLoad', function ($event, $widget) {

            $widget->position = $this->positions->find($widget->getId());

        });

        $app->on('system.widget.postSave', function ($event, $widget) {

            if ($position = isset($widget->position) ? $widget->position : false) {
                App::config('system/widget')->set('widget.positions', $this->positions->set($position, $widget->getId()));
            }

            // if (false !== $config) {
            //     App::config('system/widget')->set('widget.config.' . $widget->getId(), $config);
            // }

        });
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
        if (!$this->registered) {

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

        return $this->registered;
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
        $this->registered[$id] = compact('id', 'name', 'description');
    }

    /**
     * @return array
     */
    public function getPositionManager()
    {
        return new PositionManager($this->config('widget.positions'));
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
        return $this->types;
    }

    /**
     * Registers a type.
     *
     * @param TypeInterface $type
     */
    public function registerType(TypeInterface $type)
    {
        $this->types[$type->name] = $type;
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
