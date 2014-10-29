<?php

namespace Pagekit\Menu\Filter;

class AccessFilter extends FilterIterator
{
    /**
     * {@inheritdoc}
     */
    public function accept()
    {
        return !isset($this->options['access']) || !$this->options['access'] || parent::current()->hasAccess($this['user']);
    }
}
