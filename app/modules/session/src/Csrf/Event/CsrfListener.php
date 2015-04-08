<?php

namespace Pagekit\Session\Csrf\Event;

use Pagekit\Session\Csrf\Provider\CsrfProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
     * @param  GetResponseEvent $event
     * @throws AccessDeniedHttpException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($csrf = $request->attributes->get('_request[csrf]', false, true)
            and !$this->provider->validate($request->get(is_string($csrf) ? $csrf : '_csrf', $request->headers->get(is_string($csrf) ? $csrf : 'X-XSRF-TOKEN')))
        ) {
            throw new AccessDeniedHttpException('Invalid CSRF token.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => ['onKernelRequest', -10]
        ];
    }
}
