<?php

return [

    'up' => function() use ($app) {

        $util = $app['db']->getUtility();

        if ($util->tableExists('@system_url_alias')) {
            $util->dropTable('@system_url_alias');
        }
    }

];
