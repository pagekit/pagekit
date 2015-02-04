<?php

use Pagekit\Mail\ImpersonatePlugin;
use Pagekit\Mail\Mailer;

return [

    'name' => 'system/mail',

    'main' => function ($app, $config) {

        $app['mailer'] = function ($app) use ($config) {

            $app['mailer.initialized'] = true;

            $mailer = new Mailer($app['swift.transport'], $app['swift.spooltransport']);
            $mailer->registerPlugin(new ImpersonatePlugin($config['from_address'], $config['from_name']));

            return $mailer;
        };

        $app['mailer.initialized'] = false;

        $app['swift.transport'] = function ($app) use ($config) {

            if ('smtp' == $config['driver']) {

                $transport = new Swift_Transport_EsmtpTransport(
                    $app['swift.transport.buffer'],
                    [$app['swift.transport.authhandler']],
                    $app['swift.transport.eventdispatcher']
                );

                $transport->setHost($config['host']);
                $transport->setPort($config['port']);
                $transport->setUsername($config['username']);
                $transport->setPassword($config['password']);
                $transport->setEncryption($config['encryption']);
                $transport->setAuthMode($config['auth_mode']);

                return $transport;
            }

            if ('mail' == $config['driver']) {
                return Swift_MailTransport::newInstance();
            }

            throw new \InvalidArgumentException('Invalid mail driver.');
        };

        $app['swift.transport.buffer'] = function () {
            return new Swift_Transport_StreamBuffer(new Swift_StreamFilters_StringReplacementFilterFactory);
        };

        $app['swift.transport.authhandler'] = function () {
            return new Swift_Transport_Esmtp_AuthHandler([
                new Swift_Transport_Esmtp_Auth_CramMd5Authenticator,
                new Swift_Transport_Esmtp_Auth_LoginAuthenticator,
                new Swift_Transport_Esmtp_Auth_PlainAuthenticator,
            ]);
        };

        $app['swift.transport.eventdispatcher'] = function () {
            return new Swift_Events_SimpleEventDispatcher;
        };

        $app['swift.spool'] = function () {
            return new Swift_MemorySpool;
        };

        $app['swift.spooltransport'] = function ($app) {
            return new Swift_SpoolTransport($app['swift.spool']);
        };

        $app->on('kernel.terminate', function () use ($app) {
            if ($app['mailer.initialized']) {
                try {
                    $app['swift.spooltransport']->getSpool()->flushQueue($app['swift.transport']);
                } catch (\Exception $e) {}
            }
        });

        $app->on('system.settings.edit', function ($event) use ($app, $config) {
            $event->add('system/mail', __('Mail'), $app['view']->render('extensions/system/modules/mail/views/admin/settings.razr', ['config' => $config]));
        });
    },

    'autoload' => [

        'Pagekit\\Mail\\' => 'src'

    ],

    'controllers' => [

        '@system/mail: /system/mail' => [
            'Pagekit\\Mail\\Controller\\TestController'
        ]

    ],

    'driver'       => 'mail',
    'host'         => 'localhost',
    'port'         => 25,
    'username'     => null,
    'password'     => null,
    'encryption'   => null,
    'auth_mode'    => null,
    'from_name'    => null,
    'from_address' => null

];
