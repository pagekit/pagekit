<?php

return [

    'up' => function() use ($app) {

        $util = $app['db']->getUtility();

        if ($util->tableExists('@page_page') === false) {
            $util->createTable('@page_page', function($table) {
                $table->addColumn('id', 'integer', array('unsigned' => true, 'length' => 10, 'autoincrement' => true));
                $table->addColumn('title', 'string', array('length' => 255));
                $table->addColumn('content', 'text');
                $table->addColumn('url', 'string', array('length' => 1023));
                $table->addColumn('status', 'smallint');
                $table->addColumn('data', 'json_array', array('notnull' => false));
                $table->addColumn('roles', 'simple_array', array('notnull' => false));
                $table->setPrimaryKey(array('id'));
            });
        }
    }

];
