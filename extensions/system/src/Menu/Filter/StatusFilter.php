<?php

namespace Pagekit\Menu\Filter;

class StatusFilter extends FilterIterator
{
    /**
     * @var integer
     */
    protected $status;

    /**
     * {@inheritdoc}
     */
    public function __construct(\Iterator $iterator, array $options = [])
    {
        parent::__construct($iterator, $options);

        $this->status = isset($options['status']) ? $options['status'] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function accept()
    {
        if (null === $this->status) {
            return true;
        }

        return $this->status == parent::current()->getStatus();
    }
}
