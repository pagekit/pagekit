<?php

namespace Pagekit\Installer;

use Pagekit\Extension\Extension;
use Pagekit\Framework\Application;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InstallerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        if ($app['config']['app.installer']) {

            parent::boot($app);

            $app->error(function(NotFoundHttpException $e) use ($app) {
                return $app['response']->redirect('@installer/installer');
            });

            $app->on('system.loaded', function() use ($app) {

                if ($locale = $app['request']->getPreferredLanguage()) {
                    $app['translator']->setLocale($locale);
                }

                $app['view.scripts']->register('requirejs', 'extension://system/assets/js/require.min.js', 'requirejs-config');
                $app['view.scripts']->register('requirejs-config', 'extension://system/assets/js/require.js');
            });
        }
    }
}
