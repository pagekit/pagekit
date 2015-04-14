<?php

namespace Pagekit\Profiler\Event;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ToolbarListener implements EventSubscriberInterface
{
    public function onKernelRequest()
    {
        App::callbacks()->get('_profiler/{token}', '_profiler', function ($token) {

            if (!$profile = App::profiler()->loadProfile($token)) {
                return new Response;
            }

            return new Response(App::view(__DIR__.'/../../views/toolbar.php', ['profiler' => App::profiler(), 'profile' => $profile, 'token' => $token]));

        })->setDefault('_maintenance', true);
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request  = $event->getRequest();

        if ($event->isMasterRequest()
            && !$request->isXmlHttpRequest()
            && !$request->attributes->get('_disable_profiler_toolbar')
            && $response->headers->has('X-Debug-Token')
            && !$response->isRedirection()
            && !($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
            && 'html' === $request->getRequestFormat()
        ) {
            $this->injectToolbar($response);
        }
    }

    /**
     * Injects the web debug toolbar into the given Response.
     *
     * @param Response $response A Response instance
     */
    protected function injectToolbar(Response $response)
    {
        $content = $response->getContent();

        if (false === $pos = strripos($content, '</body>')) {
            return;
        }

        $token    = $response->headers->get('X-Debug-Token');
        $route    = App::url()->getRoute('_profiler', compact('token'));
        $url      = App::file()->getUrl(__DIR__.'/../../assets');
        $markup[] = "<div id=\"profiler\" data-url=\"{$url}\" data-route=\"{$route}\" style=\"display: none;\"></div>";
        $markup[] = "<script src=\"{$url}/js/profiler.js\"></script>\n";

        $response->setContent(substr_replace($content, implode("\n", $markup), $pos, 0));
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'kernel.request'  => ['onKernelRequest', 100],
            'kernel.response' => ['onKernelResponse', -100]
        ];
    }
}
