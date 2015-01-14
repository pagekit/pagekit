<?php

return [

    'up' => function() use ($app) {

        $util = $app['db']->getUtility();

        if ($util->tableExists('@tree_node') === false) {
            $util->createTable('@tree_node', function($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('parent_id', 'integer', ['unsigned' => true, 'length' => 10]);
                $table->addColumn('priority', 'integer', ['default' => 0]);
                $table->addColumn('status', 'smallint');
                $table->addColumn('title', 'string', ['length' => 255]);
                $table->addColumn('slug', 'string', ['length' => 1023]);
                $table->addColumn('path', 'string', ['length' => 1023]);
                $table->addColumn('roles', 'simple_array', ['notnull' => false]);
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
            });
        }
    }

];
