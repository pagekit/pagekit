<?php

namespace Pagekit\Tree\Annotation;

/**
 * @Annotation
 */
class Route
{
    /**
     * @var string
     */
    protected $mount;

    /**
     * Constructor.
     *
     * @param  array $data
     * @throws \BadMethodCallException
     */
    public function __construct(array $data)
    {
        if (isset($data['mount'])) {
            $this->setMount($data['mount']);
        }
    }

    /**
     * Gets the mount id.
     *
     * @return string
     */
    public function getMount()
    {
        return $this->mount;
    }

    /**
     * Sets the mount id.
     *
     * @param string
     */
    public function setMount($mount)
    {
        $this->mount = $mount;
    }
}
