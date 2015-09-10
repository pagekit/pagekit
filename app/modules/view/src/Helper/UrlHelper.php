<?php

namespace Pagekit\View\Helper;

use Pagekit\Application\UrlProvider;
use Pagekit\Routing\Generator\UrlGenerator;

class UrlHelper extends Helper
{
    /**
     * @var UrlProvider
     */
    protected $provider;

    /**
     * Constructor.
     *
     * @param UrlProvider $provider
     */
    public function __construct(UrlProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Get shortcut.
     *
     * @see get()
     */
    public function __invoke($path = '', $parameters = [], $referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        return $this->provider->get($path, $parameters, $referenceType);
    }

    /**
     * Proxies all method calls to the provider.
     *
     * @param  string $method
     * @param  array  $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!is_callable($callable = [$this->provider, $method])) {
            throw new \InvalidArgumentException(sprintf('Undefined method call "%s::%s"', get_class($this->provider), $method));
        }

        return call_user_func_array($callable, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'url';
    }
}
