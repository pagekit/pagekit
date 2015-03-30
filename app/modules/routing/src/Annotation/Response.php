<?php

namespace Pagekit\Routing\Annotation;

/**
 * @Annotation
 */
class Response
{
    public $data;

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Returns the data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
