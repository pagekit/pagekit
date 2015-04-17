<?php

namespace Pagekit\Kernel\Event;

use Pagekit\Event\EventSubscriberInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @copyright Copyright (c) 2004-2015 Fabien Potencier
 */
class ResponseListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $charset;

    /**
     * Constructor.
     *
     * @param string $charset
     */
    public function __construct($charset = 'UTF-8')
    {
        $this->charset = $charset;
    }

    /**
     * Filters the Response.
     *
     * @param $event
     */
    public function onResponse($event, $request)
    {
        if (!$event->isMasterRequest() || !$response = $event->getResponse()) {
            return;
        }

        if ($response->getCharset() === null) {
            $response->setCharset($this->charset);
        }

        $response->prepare($request);
    }

    public function subscribe()
    {
        return [
            'kernel.response' => ['onResponse', -10]
        ];
    }
}
