<?php

use Pagekit\Mail\ImpersonatePlugin;
use Pagekit\Mail\Mailer;

return [

    'name' => 'system/mail',

    'main' => function ($app) {

        $app['mailer'] = function ($app) {

            $app['mailer.initialized'] = true;

            $mailer = new Mailer($app['swift.transport'], $app['swift.spooltransport']);
            $mailer->registerPlugin(new ImpersonatePlugin($this->config['from_address'], $this->config['from_name']));

            return $mailer;
        };

        $app['mailer.initialized'] = false;

        $app['swift.transport'] = function ($app) {

            if ('smtp' == $this->config['driver']) {

                $transport = new Swift_Transport_EsmtpTransport(
                    $app['swift.transport.buffer'],
                    [$app['swift.transport.authhandler']],
                    $app['swift.transport.eventdispatcher']
                );

                $transport->setHost($this->config['host']);
                $transport->setPort($this->config['port']);
                $transport->setUsername($this->config['username']);
                $transport->setPassword($this->config['password']);
                $transport->setEncryption($this->config['encryption']);
                $transport->setAuthMode($this->config['auth_mode']);

                return $transport;
            }

            if ('mail' == $this->config['driver']) {
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

        $app->on('system.settings.edit', function ($event) use ($app) {
            $event->add('system/mail', __('Mail'), $app['tmpl']->render('extensions/system/modules/mail/views/admin/settings.razr', ['config' => $this->config]));
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

    'config' => [

        'driver'       => 'mail',
        'host'         => 'localhost',
        'port'         => 25,
        'username'     => null,
        'password'     => null,
        'encryption'   => null,
        'auth_mode'    => null,
        'from_name'    => null,
        'from_address' => null

    ]
];
