<?php

namespace Pagekit\Dashboard;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\Dashboard\Widget\FeedWidget;
use Pagekit\Dashboard\Widget\WeatherWidget;

class DashboardModule extends Module
{
    protected $types;

    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
    }

    /**
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
