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

        if ($site->config('maintenance.enabled') && !(App::isAdmin() || $request->attributes->get('_maintenance') || App::user()->hasAccess('site: maintenance access') || App::user()->hasAccess('system: access admin area'))) {

            $message = $site->config('maintenance.msg') ?: __("We'll be back soon.");
            $logo = $site->config('maintenance.logo') ?: 'app/system/assets/images/pagekit-logo-large-black.svg';
            $response = App::view('system/theme:views/maintenance.php', compact('message', 'logo'));

            $request->attributes->set('_disable_debugbar', true);

            $types = $request->getAcceptableContentTypes();

            if (!App::user()->isAuthenticated() && $request->isXMLHttpRequest()) {
                App::abort('401', 'Unauthorized');
            } elseif ('json' == $request->getFormat(array_shift($types))) {
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
