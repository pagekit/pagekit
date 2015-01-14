<?php

namespace Pagekit\Menu\Filter;

abstract class FilterIterator extends \FilterIterator
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor.
     *
     * @param \Iterator $iterator
     * @param array     $options
     */
    public function __construct(\Iterator $iterator, array $options = [])
    {
        parent::__construct($iterator);

        $this->options = $options;
    }
}
