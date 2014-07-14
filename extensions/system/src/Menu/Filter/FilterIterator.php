<?php

namespace Pagekit\Menu\Filter;

use Pagekit\Framework\ApplicationTrait;

abstract class FilterIterator extends \FilterIterator implements \ArrayAccess
{
    use ApplicationTrait;

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
