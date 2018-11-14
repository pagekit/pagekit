<?php

namespace Pagekit\Captcha\Annotation;

/**
 * @Annotation
 */
class Captcha
{
    /**
     * @var string
     */
    protected $route = null;

    /**
     * @var bool
     */
    protected $verify = false;

    /**
     * Constructor.
     *
     * @param  array $data
     * @throws \BadMethodCallException
     */
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {

            if (!method_exists($this, $method = 'set'.$key)) {
                throw new \BadMethodCallException(sprintf("Unknown property '%s' on annotation '%s'.", $key, get_class($this)));
            }

            $this->$method($value);
        }
    }

    /**
     * Gets the captcha route.
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Sets the captcha route.
     *
     * @param string
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * Gets verify option.
     *
     * @return bool
     */
    public function getVerify()
    {
        return $this->verify;
    }

    /**
     * Sets the verify option.
     *
     * @param bool $verify
     */
    public function setVerify($verify)
    {
        $this->verify = $verify;
    }
}
