<?php

return [

    'up' => function() use ($app) {

        $util = $app['db']->getUtility();

        if ($util->tableExists('@tree_page') === false) {
            $util->createTable('@tree_page', function($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('parent_id', 'integer', ['unsigned' => true, 'length' => 10]);
                $table->addColumn('priority', 'integer', ['default' => 0]);
                $table->addColumn('title', 'string', ['length' => 255]);
                $table->addColumn('slug', 'string', ['length' => 1023]);
                $table->addColumn('path', 'string', ['length' => 1023]);
                $table->addColumn('mount', 'string', ['length' => 255, 'notnull' => false]);
                $table->addColumn('url', 'string', ['length' => 1023]);
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->addColumn('roles', 'simple_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
            });
        }
    }

];
