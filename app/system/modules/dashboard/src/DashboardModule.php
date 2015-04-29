<?php

namespace Pagekit\Dashboard;

use Pagekit\Application as App;
use Pagekit\Dashboard\Widget\FeedWidget;
use Pagekit\Dashboard\Widget\WeatherWidget;
use Pagekit\Module\Module;
use Pagekit\User\Entity\User;
use Pagekit\Widget\Model\Type;
use Pagekit\Widget\Model\TypeInterface;

class DashboardModule extends Module
{
    protected $types;

    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $this->types = [];

        $app->on('widget.types', function($event, $widgets) {
            $widgets->registerType(new FeedWidget());
            $widgets->registerType(new WeatherWidget());
        });
    }

    /**
     * Gets a widget.
     *
     * @param  string $id
     * @return array
     */
    public function getWidget($id)
    {
        $widgets = $this->getWidgets();

        return isset($widgets[$id]) ? $widgets[$id] : null;
    }

    /**
     * Gets all user widgets.
     *
     * @return array
     */
    public function getWidgets()
    {
        $widgets  = [];
        $defaults = $this->config('defaults');

        foreach (App::user()->get('dashboard', $defaults) as $id => $widget) {
            if ($type = $this->getType($widget['type'])) {
                $widgets[$id] = array_merge(['title' => $type->getName()], $widget);
            }
        }

        return $widgets;
    }

    /**
     * Save widgets on user.
     *
     * @param array $widgets
     */
    public function saveWidgets(array $widgets)
    {
        $id = App::user()->getId();

        if ($user = User::find($id)) {
            $user->set('dashboard', $widgets);
            $user->save();
        }
    }

    /**
     * Gets a widget type.
     *
     * @param  string $id
     * @return Type
     */
    public function getType($id)
    {
        return App::module('system/widget')->getType($id);
    }

    /**
     * Gets registered widget types.
     *
     * @return array
     */
    public function getTypes()
    {
        return App::module('system/widget')->getTypes('dashboard');
    }
}
