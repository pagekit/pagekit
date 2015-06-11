<?php

return [

    'up' => function() use ($app) {

        $util = $app['db']->getUtility();

        if ($util->tableExists('@hello_greetings') === false) {
            $util->createTable('@hello_greetings', function($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('name', 'string', ['length' => 255, 'default' => '']);
                $table->setPrimaryKey(['id']);
            });
        }
    },

    'down' => function() use ($app) {

        $util = $app['db']->getUtility();

        if ($util->tableExists('@hello_greetings')) {
            $util->dropTable('@hello_greetings');
        }
    }

];
