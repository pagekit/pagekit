<?php

namespace Pagekit\Menu\Filter;

class AccessFilter extends FilterIterator
{
    /**
     * {@inheritdoc}
     */
    public function accept()
    {
        return !isset($this->options['access']) or !$this->options['access'] or parent::current()->hasAccess($this['user']);
    }
}
