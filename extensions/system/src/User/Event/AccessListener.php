<?php

namespace Pagekit\User\Event;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Pagekit\Component\Auth\Event\AuthorizeEvent;
use Pagekit\Component\Auth\Exception\AuthException;
use Pagekit\Component\Routing\Event\ConfigureRouteEvent;
use Pagekit\Component\Routing\Event\RouteCollectionEvent;
use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\User\Annotation\Access;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class AccessListener extends EventSubscriber
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
     *
     * @param ConfigureRouteEvent $event
     */
    public function onConfigureRoute(ConfigureRouteEvent $event)
    {
        if (!$this->reader) {
            $this->reader = new SimpleAnnotationReader;
            $this->reader->addNamespace('Pagekit\User\Annotation');
        }

        $access = [];
        $route  = $event->getRoute();

        foreach (array_merge($this->reader->getClassAnnotations($event->getClass()), $this->reader->getMethodAnnotations($event->getMethod())) as $annot) {
            if ($annot instanceof Access) {

                if ($expression = $annot->getExpression()) {
                    $access[] = $expression;
                }

                if (null !== $annot->getAdmin()) {
                    $route->setDefault('_admin', $admin = $annot->getAdmin());

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
        if (strpos($this['request']->get('redirect'), $this['url']->route('@system/admin', [], true)) === 0 && !$event->getUser()->hasAccess('system: access admin area')) {
            throw new AuthException(__('You do not have access to the administration area of this site.'));
        }
    }

    /**
     * Reads the access expressions and evaluates them on the current user.
     *
     * @param GetResponseEvent $event
     */
    public function onSystemLoaded(GetResponseEvent $event)
    {
        if ($access = $event->getRequest()->attributes->get('_access')) {
            foreach ($access as $expression) {
                if (!$this['user']->hasAccess($expression)) {
                    $event->setResponse($this['response']->create(__('Insufficient User Rights.'), 403));
                    break;
                }
            }
        }
    }

    /**
     * Checks for the "system: access admin area" and redirects to login.
     *
     * @param GetResponseEvent $event
     */
    public function onLoadAdmin(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$this['auth']->getUser() and $access = $request->attributes->get('_access') and in_array('system: access admin area', $access)) {

            $params = [];

            // redirect to default URL for POST requests and don't explicitly redirect the default URL
            if ('POST' !== $request->getMethod() && $request->attributes->get('_route') != '@system/admin') {
                $params['redirect'] = $this['url']->current(true);
            }

            $event->setResponse($this['response']->redirect('@system/admin/login', $params));
        }
    }

    /**
     * Prepends "/admin" path.
     *
     * @param RouteCollectionEvent $event
     */
    public function getRoutes(RouteCollectionEvent $event)
    {
        foreach ($event->getRoutes() as $route) {
            if ($route->getDefault('_admin')) {
                $route->setPath('admin'.rtrim($route->getPath(), '/'));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'route.configure'  => 'onConfigureRoute',
            'route.collection' => ['getRoutes', -32],
            'auth.authorize'   => 'onAuthorize',
            'system.loaded'    => [
                ['onSystemLoaded', -512],
                ['onLoadAdmin', -256]
            ]
        ];
    }
}
