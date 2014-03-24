<?php

namespace Pagekit\Theme\Event;

use Pagekit\Framework\Event\EventSubscriber;

class ThemeListener extends EventSubscriber
{
    /**
     * Loads the site/admin theme.
     */
    public function onBoot()
    {
        try {

            self::$app['theme.admin'] = $this('themes')->load('system', 'extension://system/theme');
            self::$app['theme.site']  = $this('themes')->load($this('config')->get('theme.site'));

            if ($this('isAdmin')) {
                $this('view')->setLayout($this('theme.admin')->boot(self::$app)->getLayout());
            } else {
                $this('view')->setLayout($this('theme.site')->boot(self::$app)->getLayout());
            }

        } catch (\Exception $e) {}
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'boot' => array('onBoot', 16)
        );
    }
}
