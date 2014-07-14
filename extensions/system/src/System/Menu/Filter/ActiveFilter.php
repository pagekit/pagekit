<?php

namespace Pagekit\System\Menu\Filter;

use Pagekit\Menu\Filter\FilterIterator;

class ActiveFilter extends FilterIterator
{
    /**
     * @var string
     */
    protected $route;

    /**
     * {@inheritdoc}
     */
    public function __construct(\Iterator $iterator, array $options = [])
    {
        parent::__construct($iterator, $options);

        $this->route = $this['request']->attributes->get('_route');
    }

    /**
     * {@inheritdoc}
     */
    public function accept()
    {
        $item = parent::current();

        if ($active = $item->getAttribute('active') and is_string($active)) {

            $active = (bool) preg_match('#^'.str_replace('*', '.*', $active).'$#', $this->route);

            $item->setAttribute('active', $active);

            if ($active) {
                while ($item->getParentId() && $item = $item->getMenu()->getItem($item->getParentId())) {
                    $item->setAttribute('active', $active);
                }
            }
        }

        return true;
    }
}
