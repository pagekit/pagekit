<?php

namespace Pagekit\Dashboard;

use Pagekit\Application as App;
use Pagekit\Dashboard\Widget\FeedWidget;
use Pagekit\Dashboard\Widget\WeatherWidget;
use Pagekit\Module\Module;
use Pagekit\User\Entity\User;

class DashboardModule extends Module
{
    protected $types;

    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $this->types = [];
    }

    /**
     * Gets a widget.
     *
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
        $defaults = App::system()->config('dashboard.default');

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
     * @return Type
     */
    public function getType($id)
    {
        $types = $this->getTypes();

        return isset($types[$id]) ? $types[$id] : null;
    }

    /**
     * Gets registered widget types.
     *
     * @return array
     */
    public function getTypes()
    {
        if (!$this->types) {

            $this->registerType(new FeedWidget);
            $this->registerType(new WeatherWidget);

            App::trigger('system.dashboard', [$this]);
        }

        return $this->types;
    }

    /**
     * Register a widget type.
     *
     * @param string|TypeInterface $type
     */
    public function registerType($type)
    {
        if (!is_subclass_of($type, 'Pagekit\Widget\Model\TypeInterface')) {
            throw new \RuntimeException(sprintf('The widget %s does not implement TypeInterface', $type));
        }

        if (is_string($type)) {
            $type = new $type;
        }

        $this->types[$type->getId()] = $type;
    }
}
