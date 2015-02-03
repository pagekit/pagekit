<?php

namespace Pagekit\Installer;

use Pagekit\Application as App;
use Pagekit\System\Extension;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InstallerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function __construct(App $app, array $config)
    {
        if (!$app['config']['app.installer']) {
            return;
        }

        parent::__construct($app, $config);

        $app->error(function (NotFoundHttpException $e) use ($app) {
            return $app['response']->redirect('@installer/installer');
        });

        $app->on('system.loaded', function () use ($app) {
            if ($locale = $app['request']->getPreferredLanguage()) {
                $app['translator']->setLocale($locale);
            }
        });
    }
}
