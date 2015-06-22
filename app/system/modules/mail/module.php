<?php

use Pagekit\Mail\Mailer;
use Pagekit\Mail\Plugin\ImpersonatePlugin;

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

    },

    'autoload' => [

        'Pagekit\\Mail\\' => 'src'

    ],

    'routes' => [

        '/system' => [
            'name' => '@system',
            'controller' => 'Pagekit\\Mail\\Controller\\MailController'
        ]

    ],

    'events' => [

        'terminate' => function () use ($app) {

            if ($app['mailer.initialized']) {
                try {
                    $app['swift.spooltransport']->getSpool()->flushQueue($app['swift.transport']);
                } catch (\Exception $e) {
                }
            }

        },

        'view.system:modules/settings/views/settings' => function ($event, $view) use ($app) {
            $view->data('$mail', ['ssl' => extension_loaded('openssl')]);
            $view->data('$settings', ['options' => [$this->name => $this->config]]);
            $view->script('settings-mail', 'app/system/modules/mail/app/bundle/settings.js', 'settings');
        }

    ],

    'config' => [

        'driver' => 'mail',
        'host' => 'localhost',
        'port' => 25,
        'username' => null,
        'password' => null,
        'encryption' => null,
        'auth_mode' => null,
        'from_name' => null,
        'from_address' => null

    ]

];
