<?php

use Pagekit\Intl\Intl;
use Pagekit\Intl\Helper\DateHelper;
use Pagekit\Intl\Loader\ArrayLoader;
use Pagekit\Intl\Loader\MoFileLoader;
use Pagekit\Intl\Loader\PhpFileLoader;
use Pagekit\Intl\Loader\PoFileLoader;
use Symfony\Component\Translation\Translator;

return [

    'name' => 'intl',

    'main' => function ($app) {

        $app['intl'] = function () {

            $intl = Intl::getInstance();
            $intl['date'] = new DateHelper();

            return $intl;
        };

        $app['intl']->setDefaultLocale($this->config['locale']);

        $app['translator'] = function () {

            $translator = new Translator($this->config['locale']);
            $translator->addLoader('php', new PhpFileLoader);
            $translator->addLoader('mo', new MoFileLoader);
            $translator->addLoader('po', new PoFileLoader);
            $translator->addLoader('array', new ArrayLoader);

            return $translator;
        };

        require __DIR__.'/functions.php';

    },

    'autoload' => [

        'Pagekit\\Intl\\' => 'src'

    ],

    'config' => [

        'locale' => 'en_US'

    ]

];
