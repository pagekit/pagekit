<?php

namespace Pagekit\Menu\Model;

class Node extends \Pagekit\Component\Tree\Node
{
    /**
     * @var ItemInterface
     */
    protected $item;

    /**
     * @param ItemInterface $item
     */
    public function setItem(ItemInterface $item)
    {
        $this->item = $item;
    }

    /**
     * @return ItemInterface
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function getAttribute($key, $default = null)
    {
        return $this->item->getAttribute($key, $default);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->item->getName();
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->item->getUrl();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->item;
    }

    /**
     * @param  string $method
     * @param  array  $args
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __call($method, $args)
    {
        if (!$this->item) {
            return;
        }

        if (!is_callable($callable = [$this->item, $method])) {
            throw new \InvalidArgumentException(sprintf('Undefined method call "%s::%s"', get_class($this->item), $method));
        }

        return call_user_func_array($callable, $args);
    }
}
