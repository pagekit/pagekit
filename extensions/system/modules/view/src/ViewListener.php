<?php

namespace Pagekit\View;

use Pagekit\Application as App;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ViewListener implements EventSubscriberInterface
{
    /**
     * Registers system assets.
     */
    public function onSystemLoaded()
    {
        $debug   = App::module('framework')->config('debug');
        $pagekit = ['version' => App::version(), 'url' => App::router()->getContext()->getBaseUrl(), 'csrf' => App::csrf()->generate()];

        $scripts = App::scripts();
        $scripts->register('angular', 'vendor/assets/angular/angular.min.js', 'jquery');
        $scripts->register('angular-animate', 'vendor/assets/angular-animate/angular-animate.min.js', 'angular');
        $scripts->register('angular-cookies', 'vendor/assets/angular-cookies/angular-cookies.min.js', 'angular');
        $scripts->register('angular-loader', 'vendor/assets/angular-loader/angular-loader.min.js', 'angular');
        $scripts->register('angular-messages', 'vendor/assets/angular-messages/angular-messages.min.js', 'angular');
        $scripts->register('angular-resource', 'vendor/assets/angular-resource/angular-resource.min.js', 'angular');
        $scripts->register('angular-route', 'vendor/assets/angular-route/angular-route.min.js', 'angular');
        $scripts->register('angular-sanitize', 'vendor/assets/angular-sanitize/angular-sanitize.min.js', 'angular');
        $scripts->register('angular-touch', 'vendor/assets/angular-touch/angular-touch.min.js', 'angular');
        $scripts->register('application', 'extensions/system/app/application.js', 'angular');
        $scripts->register('application-directives', 'extensions/system/app/directives.js', 'application');
        $scripts->register('jquery', 'vendor/assets/jquery/dist/jquery.min.js', [], ['requirejs' => true]);
        $scripts->register('requirejs', 'extensions/system/assets/js/require.min.js', 'requirejs-config');
        $scripts->register('requirejs-config', 'extensions/system/assets/js/require.js', 'pagekit');
        $scripts->register('uikit', 'vendor/assets/uikit/js/uikit.min.js', 'jquery', ['requirejs' => true]);
        $scripts->register('uikit-autocomplete', 'vendor/assets/uikit/js/components/autocomplete.min.js', 'uikit', ['requirejs' => true]);
        $scripts->register('uikit-nestable', 'vendor/assets/uikit/js/components/nestable.min.js', 'uikit', ['requirejs' => true]);
        $scripts->register('uikit-notify', 'vendor/assets/uikit/js/components/notify.min.js', 'uikit', ['requirejs' => true]);
        $scripts->register('uikit-sortable', 'vendor/assets/uikit/js/components/sortable.min.js', 'uikit', ['requirejs' => true]);
        $scripts->register('uikit-sticky', 'vendor/assets/uikit/js/components/sticky.min.js', 'uikit', ['requirejs' => true]);
        $scripts->register('uikit-form-password', 'vendor/assets/uikit/js/components/form-password.min.js', 'uikit', ['requirejs' => true]);
        $scripts->register('uikit-pagination', 'vendor/assets/uikit/js/components/pagination.min.js', 'uikit');
        $scripts->register('gravatar', 'vendor/assets/gravatarjs/gravatar.js');
        $scripts->register('system', 'extensions/system/app/system.js', ['jquery', 'locale'])->addData('pagekit', $pagekit);
        $scripts->register('vue', 'vendor/assets/vue/dist/'.($debug ? 'vue.js' : 'vue.min.js'));
        $scripts->register('vue-system', 'extensions/system/app/vue-system.js', ['vue', 'system', 'uikit-pagination']);

        App::sections()->set('messages', function() {
            return App::view('extensions/system/views/messages/messages.php');
        });
    }

    /**
     * Resolves requirejs dependencies.
     */
    public function onKernelResponse()
    {
        $scripts = App::scripts();

        // -TODO- remove when alternative is agreed upon

        $require = [];
        $requeue = [];

        foreach ($scripts as $script) {
            if ($script['requirejs']) {
                $require[] = $script;
            } elseif (array_key_exists('requirejs', $scripts->resolveDependencies($script))) {
                $requeue[] = $script;
            }
        }

        if (!$requeue) {
            return;
        }

        foreach ($require as $script) {
            $script['dependencies'] = array_merge((array) $script['dependencies'], ['requirejs']);
            $scripts->add($script->getName());
        }

        foreach ($requeue as $script) {
            $scripts->remove($name = $script->getName());
            $scripts->add($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.loaded'   => 'onSystemLoaded',
            'kernel.response' => ['onKernelResponse', 15]
        ];
    }
}
