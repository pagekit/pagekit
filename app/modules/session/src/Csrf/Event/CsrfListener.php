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
        $this->provider->setToken($request->get('_csrf', $request->headers->get('X-XSRF-TOKEN')));

        if ($csrf = $request->attributes->get('_request[csrf]', false, true) and !$this->provider->validate()) {
            throw new \RuntimeException('Invalid CSRF token.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'request' => ['onRequest', -150]
        ];
    }
}
