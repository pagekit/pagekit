<?php

namespace Pagekit\System\Menu\Filter;

use Pagekit\Menu\Filter\FilterIterator;

class ActiveFilter extends FilterIterator
{
    /**
     * @var string
     */
    protected $path;

    /**
     * {@inheritdoc}
     */
    public function __construct(\Iterator $iterator, array $options = array())
    {
        parent::__construct($iterator, $options);

        $this->path = $this('request')->getPathInfo();
    }

    /**
     * {@inheritdoc}
     */
    public function accept()
    {
        $item = parent::current();

        if ($active = $item->getAttribute('active') and is_string($active)) {
            $item->setAttribute('active', (bool) preg_match('#^'.str_replace('*', '.*', $active).'$#', $this->path));
        }

        return true;
    }
}
