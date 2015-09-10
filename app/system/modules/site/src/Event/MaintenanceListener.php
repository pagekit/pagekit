<?php

namespace Pagekit\Site\Event;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;

class MaintenanceListener implements EventSubscriberInterface
{
    /**
     * Puts the page in maintenance mode.
     */
    public function onRequest($event, $request)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $site = App::module('system/site');

        if ($site->config('maintenance.enabled') && !(App::isAdmin() || $request->attributes->get('_maintenance') || App::user()->hasAccess('site: maintenance access'))) {

            $message  = $site->config('maintenance.msg') ?: __("We'll be back soon.");
            $response = App::view('system/theme:views/maintenance.php', compact('message'));

            $request->attributes->set('_disable_debugbar', true);

            $types = $request->getAcceptableContentTypes();

            if ('json' == $request->getFormat(array_shift($types))) {
                $response = App::response()->json($message, 503);
            } else {
                $response = App::response($response, 503);
            }

            $event->setResponse($response);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'request' => ['onRequest', 10]
        ];
    }
}
