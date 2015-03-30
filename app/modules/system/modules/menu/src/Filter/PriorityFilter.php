<?php

namespace Pagekit\Menu\Filter;

class PriorityFilter extends FilterIterator
{
    /**
     * {@inheritdoc}
     */
    public function __construct(\Iterator $iterator, array $options = [])
    {
        $elements = iterator_to_array($iterator, false);

        $iterator->uasort(function($a, $b) use ($elements) {

            $priorityA = (int) $a->getPriority();
            $priorityB = (int) $b->getPriority();

            if ($priorityA == $priorityB) {
                $priorityA = array_search($a, $elements);
                $priorityB = array_search($b, $elements);
            }

            return ($priorityA < $priorityB) ? -1 : 1;
        });

        parent::__construct($iterator, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function accept()
    {
        return true;
    }
}
