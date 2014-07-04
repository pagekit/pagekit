<?php

return [

    'up' => function() use ($app) {

        $util = $app['db']->getUtility();

        if ($util->tableExists('@hello_greetings') === false) {
            $util->createTable('@hello_greetings', function($table) {
                $table->addColumn('id', 'integer', array('unsigned' => true, 'length' => 10, 'autoincrement' => true));
                $table->addColumn('name', 'string', array('length' => 255, 'default' => ''));
                $table->setPrimaryKey(array('id'));
            });
        }
    }

];
