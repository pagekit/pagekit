<?php

namespace Pagekit\Kernel\Event;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @copyright Copyright (c) 2004-2015 Fabien Potencier
 */
class FilterControllerEvent extends KernelEvent
{
    /**
     * The current controller.
     *
     * @var callable
     */
    private $controller;

    public function __construct($name, HttpKernelInterface $kernel, $controller, Request $request, $requestType)
    {
        parent::__construct($name, $kernel, $request, $requestType);

        $this->setController($controller);
    }

    /**
     * Returns the current controller.
     *
     * @return callable
     *
     * @api
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Sets a new controller.
     *
     * @param callable $controller
     *
     * @throws \LogicException
     *
     * @api
     */
    public function setController($controller)
    {
        // controller must be a callable
        if (!is_callable($controller)) {
            throw new \LogicException(sprintf('The controller must be a callable (%s given).', $this->varToString($controller)));
        }

        $this->controller = $controller;
    }

    private function varToString($var)
    {
        if (is_object($var)) {
            return sprintf('Object(%s)', get_class($var));
        }

        if (is_array($var)) {
            $a = array();
            foreach ($var as $k => $v) {
                $a[] = sprintf('%s => %s', $k, $this->varToString($v));
            }

            return sprintf('Array(%s)', implode(', ', $a));
        }

        if (is_resource($var)) {
            return sprintf('Resource(%s)', get_resource_type($var));
        }

        if (null === $var) {
            return 'null';
        }

        if (false === $var) {
            return 'false';
        }

        if (true === $var) {
            return 'true';
        }

        return (string) $var;
    }
}
