<?php

namespace Pagekit\Widget\Model;

class TypeManager implements \IteratorAggregate
{
    /**
     * @var TypeInterface[]
     */
    protected $types = array();

    /**
     * Check if a widget type is registered.
     *
     * @param  string $id
     * @return boolean
     */
    public function has($id)
    {
        return isset($this->types[$id]);
    }

    /**
     * Returns a registered widget type.
     *
     * @param  string $id The widget type id
     * @return TypeInterface|null
     */
    public function get($id)
    {
        return $this->has($id) ? $this->types[$id] : null;
    }

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

        $this->types[$type->getId()] = $type;
    }

    /**
     * Unregisters a widget type.
     *
     * @param  string  $id
     */
    public function unregister($id)
    {
        unset($this->types[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->types);
    }
}
