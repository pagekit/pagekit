<?php

namespace Pagekit\Widget;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\ApplicationTrait;
use Pagekit\Widget\Event\RegisterWidgetEvent;
use Pagekit\Widget\Model\WidgetInterface;

class WidgetProvider implements \ArrayAccess
{
    use ApplicationTrait;

    /**
     * @var mixed
     */
    protected $widgets;

    /**
     * @var RegisterWidgetEvent
     */
    protected $types;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->types = $this['events']->dispatch('system.widget', new RegisterWidgetEvent);
    }

    /**
     * Get a widget instance.
     *
     * @param  string $id
     * @return WidgetInterface
     */
    public function get($id)
    {
        return $this->widgets->find($id);
    }

    /**
     * Returns the rendered widget output, otherwise null.
     *
     * @param  mixed $widget
     * @param  array $options
     * @return string|null
     */
    public function render($widget, $options = [])
    {
        if (!$widget instanceof WidgetInterface) {
            $widget = $this->get($widget);
        }

        if ($widget && $type = $this->types[$widget->getType()]) {
            return $type->render($widget, $options);
        }
    }

    /**
     * @return RegisterWidgetEvent
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @return Repository
     */
    public function getWidgetRepository()
    {
        return $this['db.em']->getRepository('Pagekit\Widget\Entity\Widget');
    }
}
