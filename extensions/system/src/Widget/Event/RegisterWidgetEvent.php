<?php

namespace Pagekit\Widget\Event;

use Pagekit\Framework\Event\Event;
use Pagekit\Widget\Model\TypeInterface;

class RegisterWidgetEvent extends Event implements \IteratorAggregate
{
    /**
     * @var TypeInterface[]
     */
    protected $parameters = [];

    /**
     * Register a widget type.
     *
     * @param  string|TypeInterface $type The class name or instance of the type
     * @throws \RuntimeException
     */
    public function register($type)
    {
        if (!is_subclass_of($type, 'Pagekit\Widget\Model\TypeInterface')) {
            throw new \RuntimeException(sprintf('The widget %s does not implement TypeInterface', $type));
        }

        if (is_string($type)) {
            $type = new $type;
        }

        $this->parameters[$type->getId()] = $type;
    }

    /**
     * @return TypeInterface[]
     */
    public function getTypes()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->parameters);
    }
}
