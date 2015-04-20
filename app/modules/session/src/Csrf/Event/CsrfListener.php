<?php

namespace Pagekit\Session\Csrf\Event;

use Pagekit\Event\EventSubscriberInterface;
use Pagekit\Session\Csrf\Provider\CsrfProviderInterface;

class CsrfListener implements EventSubscriberInterface
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
     * Checks for the CSRF token and throws 401 exception if invalid.
     *
     * @param  $event
     * @throws \RuntimeException
     */
    public function onRequest($event, $request)
    {
        if ($csrf = $request->attributes->get('_request[csrf]', false, true)
            and !$this->provider->validate($request->get(is_string($csrf) ? $csrf : '_csrf', $request->headers->get(is_string($csrf) ? $csrf : 'X-XSRF-TOKEN')))
        ) {
            throw new \RuntimeException('Invalid CSRF token.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'app.request' => ['onRequest', -10]
        ];
    }
}
