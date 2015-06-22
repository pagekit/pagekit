<?php

namespace Pagekit\Debug;

use DebugBar\DebugBar as BaseDebugBar;
use Pagekit\Event\EventSubscriberInterface;

class DebugBar extends BaseDebugBar implements EventSubscriberInterface
{
    /**
     * Collect and save debug data.
     */
    public function onTerminate($event, $request)
    {
        $route = $request->attributes->get('_route');

        if (!$event->isMasterRequest() || $route == '_debugbar') {
            return;
        }

        $this->collect();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentRequestId()
    {
        if ($this->requestId == null) {
            $this->requestId = sha1(parent::getCurrentRequestId());
        }

        return $this->requestId;
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'terminate' => ['onTerminate', -1000]
        ];
    }
}
