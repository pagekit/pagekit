<?php

namespace Pagekit\User\Event;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Pagekit\Application as App;
use Pagekit\Auth\Event\AuthorizeEvent;
use Pagekit\Auth\Exception\AuthException;
use Pagekit\Event\EventSubscriberInterface;
use Pagekit\User\Annotation\Access;

class AccessListener implements EventSubscriberInterface
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * Constructor.
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader = null)
    {
        $this->reader = $reader;
    }

    /**
     * Reads the "@Access" annotations from the controller stores them in the "access" route option.
     */
    public function onConfigureRoute($event, $route)
    {
        if (!$this->reader) {
            $this->reader = new SimpleAnnotationReader;
            $this->reader->addNamespace('Pagekit\User\Annotation');
        }

        if (!$route->getControllerClass()) {
            return;
        }

        $access = [];

        foreach (array_merge($this->reader->getClassAnnotations($route->getControllerClass()), $this->reader->getMethodAnnotations($route->getControllerMethod())) as $annot) {
            if (!$annot instanceof Access) {
                continue;
            }

            if ($expression = $annot->getExpression()) {
                $access[] = $expression;
            }

            if ($admin = $annot->getAdmin() !== null) {

                $route->setPath('admin'.rtrim($route->getPath(), '/'));
                $permission = 'system: access admin area';

                if ($admin) {
                    $access[] = $permission;
                } else {
                    if ($key = array_search($permission, $access)) {
                        unset($access[$key]);
                    }
                }
            }
        }

        if ($access) {
            $route->setDefault('_access', array_unique($access));
        }
    }

    /**
     * Checks if the user is authorized to login to administration section.
     *
     * @param  AuthorizeEvent $event
     * @throws AuthException
     */
    public function onAuthorize(AuthorizeEvent $event)
    {
        if (strpos(App::request()->get('redirect'), App::url('@system', [], true)) === 0 && !$event->getUser()->hasAccess('system: access admin area')) {
            throw new AuthException(__('You do not have access to the administration area of this site.'));
        }
    }

    /**
     * Reads the access expressions and evaluates them on the current user.
     */
    public function onLateRequest($event, $request)
    {
        if (!$access = $request->attributes->get('_access')) {
            return;
        }

        foreach ($access as $expression) {
            if (!App::user()->hasAccess($expression)) {
                if (!App::user()->isAuthenticated()) {
                    App::abort(401, __('Unauthorized'));
                } else {
                    App::abort(403, __('Insufficient User Rights.'));
                }
            }
        }
    }

    /**
     * Checks for the "system: access admin area" and redirects to login.
     */
    public function onRequest($event, $request)
    {
        if ($request->isXmlHttpRequest() || App::auth()->getUser() || !in_array('system: access admin area', $request->attributes->get('_access', []))) {
            return;
        }

        $params = [];

        // redirect to default URL for POST requests and don't explicitly redirect the default URL
        if ('POST' !== $request->getMethod() && $request->attributes->get('_route') != '@system') {
            $params['redirect'] = App::url()->current();
        }

        $event->setResponse(App::response()->redirect('@system/login', $params));
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'route.configure' => 'onConfigureRoute',
            'auth.authorize' => 'onAuthorize',
            'request' => [
                ['onLateRequest', -100],
                ['onRequest', -50]
            ]
        ];
    }
}
