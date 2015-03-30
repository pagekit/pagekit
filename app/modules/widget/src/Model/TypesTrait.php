<?php

namespace Pagekit\Widget\Model;

trait TypesTrait
{
    /**
     * @var TypeInterface[]
     */
    protected static $types;

    /**
     * Gets a widget type.
     *
     * @param  string $type
     * @return TypeInterface|null
     */
    public function getWidgetType($type)
    {
        return isset(self::$types[$type]) ? self::$types[$type] : null;
    }

    /**
     * Gets the widget types.
     *
     * @return TypeInterface[]
     */
    public static function getWidgetTypes()
    {
        return self::$types;
    }

    /**
     * Sets the widget types.
     *
     * @param TypeInterface[] $types
     */
    public static function setWidgetTypes(array $types = [])
    {
        self::$types = $types;
    }
}
