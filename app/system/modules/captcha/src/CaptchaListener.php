<?php

namespace Pagekit\Captcha;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Pagekit\Application as App;
use Pagekit\Captcha\Annotation\Captcha;
use Pagekit\Event\EventSubscriberInterface;

class CaptchaListener implements EventSubscriberInterface
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
     * Reads the "@Captcha" annotations from the controller.
     */
    public function onConfigureRoute($event, $route)
    {
        if (!$this->reader) {
            $this->reader = new SimpleAnnotationReader;
            $this->reader->addNamespace('Pagekit\Captcha\Annotation');
        }

        if (!$route->getControllerClass()) {
            return;
        }

        $routes = [];
        foreach (array_merge($this->reader->getClassAnnotations($route->getControllerClass()), $this->reader->getMethodAnnotations($route->getControllerMethod())) as $annot) {
            if (!$annot instanceof Captcha) {
                continue;
            }

            if ($expression = $annot->getVerify()) {
                $route->setDefault('_captcha_verify', true);
            }

            if ($captchaRoute = $annot->getRoute()) {
                $routes[] = $captchaRoute;
            }
        }

        if ($routes) {
            $route->setDefault('_captcha_routes', array_unique($routes));
        }
    }

    public function onScripts($event, $scripts)
    {
        if (!App::module('system/captcha')->config('recaptcha_enable')
            || App::user()->isAuthenticated()
            || !($routes = App::request()->attributes->get('_captcha_routes'))
            || !($sitekey = App::module('system/captcha')->config('recaptcha_sitekey'))
        ) {
            return;
        }

        $routes = array_filter(array_map(function ($route) {
            if ($route = App::router()->getRoute($route)) {
                return ltrim($route->getPath(), '/');
            }
            return false;
        }, $routes));

        $scripts->register('captcha-config', sprintf('var $captcha = %s;', json_encode([
                'grecaptcha' => App::module('system/captcha')->config('recaptcha_sitekey'),
                'routes' => $routes
            ])
        ), [], 'string');
        $scripts->add('captcha-interceptor', 'system/captcha:app/bundle/captcha-interceptor.js', ['vue', 'captcha-config']);
    }

    public function onRequest($event, $request)
    {
        if (!App::module('system/captcha')->config('recaptcha_enable')
            || !($captcha = $request->attributes->get('_captcha_verify'))
            || App::user()->isAuthenticated()) {
            return;
        }

        if ($error = $this->verifyToken($request->get('gRecaptchaResponse'), App::module('system/captcha')->config('recaptcha_secret'))) {
            App::abort(400, $error);
        }
    }

    protected function verifyToken($gRecaptchaResponse, $secret)
    {
        if ($gRecaptchaResponse && $secret) {
            $result = json_decode($this->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $gRecaptchaResponse,
            ]), true);
            if (!$result['success']) {
                return __('Invalid reCaptcha.');
            }
        } else {
            return __('reCaptcha not probably configured.');
        }
    }

    protected function post($url, $parameter)
    {
        $ch = curl_init($url);
        $parameterQuery = http_build_query($parameter);

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_POST => count($parameter),
            CURLOPT_POSTFIELDS => $parameterQuery
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'route.configure' => 'onConfigureRoute',
            'request' => ['onRequest', -100],
            'view.scripts' => ['onScripts', 100]
        ];
    }
}
