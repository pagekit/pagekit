<?php

namespace Pagekit\Templating\Helper;

use Pagekit\Session\Csrf\Provider\CsrfProviderInterface;
use Symfony\Component\Templating\Helper\Helper;

class TokenHelper extends Helper
{
    /**
     * @var CsrfProviderInterface
     */
    protected $provider;

    /**
     * Constructor.
     *
     * @param CsrfProviderInterface $provider
     */
    public function __construct(CsrfProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Displays a hidden token field to reduce the risk of CSRF exploits.
     *
     * @param string $name
     */
    public function generate($name = '_csrf')
    {
        printf('<input type="hidden" name="%s" value="%s">', $name, $this->provider->generate());
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'token';
    }
}
