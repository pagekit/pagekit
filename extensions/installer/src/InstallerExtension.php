<?php

namespace Pagekit\Installer;

use Pagekit\Framework\Application;
use Pagekit\Package\Extension;
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
                return $app['response']->redirect('@installer/installer/index');
            });

            $app->on('kernel.request', function() use ($app) {

                if ($locale = $app['request']->getPreferredLanguage()) {
                    $app['translator']->setLocale($locale);
                }

                $app['view.scripts']->register('requirejs', 'vendor://assets/requirejs/require.min.js', array('requirejs-config'));
                $app['view.scripts']->register('requirejs-config', 'extension://system/assets/js/require.js');
            });
        }
    }
}
