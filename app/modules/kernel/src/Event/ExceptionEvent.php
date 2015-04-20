<?php

namespace Pagekit\Kernel\Event;

class ExceptionEvent extends KernelEvent
{
    use ResponseTrait;

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * Construct.
     *
     * @param string     $name
     * @param int        $requestType
     * @param \Exception $e
     */
    public function __construct($name, $requestType, \Exception $e)
    {
        parent::__construct($name, $requestType);

        $this->setException($e);
    }

    /**
     * Gets the thrown exception.
     *
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Sets the thrown exception.
     *
     * @param \Exception $exception
     */
    public function setException(\Exception $exception)
    {
        $this->exception = $exception;
    }
}
