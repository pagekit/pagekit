<?php

use Pagekit\Mail\Mailer;
use Pagekit\Mail\ImpersonatePlugin;
use Swift_Events_SimpleEventDispatcher;
use Swift_MailTransport;
use Swift_MemorySpool;
use Swift_SpoolTransport;
use Swift_StreamFilters_StringReplacementFilterFactory;
use Swift_Transport_Esmtp_Auth_CramMd5Authenticator;
use Swift_Transport_Esmtp_Auth_LoginAuthenticator;
use Swift_Transport_Esmtp_Auth_PlainAuthenticator;
use Swift_Transport_Esmtp_AuthHandler;
use Swift_Transport_EsmtpTransport;
use Swift_Transport_StreamBuffer;

return [

    'name' => 'system/mail',

    'main' => function ($app) {

        $app['mailer'] = function ($app) {

            $app['mailer.initialized'] = true;

            $address = $app['config']->get('mail.from.address');
            $name    = $app['config']->get('mail.from.name');

            $mailer = new Mailer($app['swift.transport'], $app['swift.spooltransport']);
            $mailer->registerPlugin(new ImpersonatePlugin($address, $name));

            return $mailer;
        };

        $app['mailer.initialized'] = false;

        $app['swift.transport'] = function ($app) {

            $driver = $app['config']->get('mail.driver');

            if ('smtp' == $driver) {

                $transport = new Swift_Transport_EsmtpTransport(
                    $app['swift.transport.buffer'],
                    [$app['swift.transport.authhandler']],
                    $app['swift.transport.eventdispatcher']
                );

                $options = array_replace([
                    'host'       => 'localhost',
                    'port'       => 25,
                    'username'   => '',
                    'password'   => '',
                    'encryption' => null,
                    'auth_mode'  => null,
                ], $app['config']->get('mail', []));

                $transport->setHost($options['host']);
                $transport->setPort($options['port']);
                $transport->setEncryption($options['encryption']);
                $transport->setUsername($options['username']);
                $transport->setPassword($options['password']);
                $transport->setAuthMode($options['auth_mode']);

                return $transport;
            }

            if ('mail' == $driver) {
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
    },

    'autoload' => [

        'Pagekit\\Mail\\' => 'src'

    ]

];
