<?php

return [

    'up' => function() use ($app) {

        $util = $app['db']->getUtility();

        if ($util->tableExists('@page_page') === false) {
            $util->createTable('@page_page', function($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('title', 'string', ['length' => 255]);
                $table->addColumn('content', 'text');
                $table->addColumn('url', 'string', ['length' => 1023]);
                $table->addColumn('status', 'smallint');
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->addColumn('roles', 'simple_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
            });
        }

        // skip migrations and return latest version
        return '0000-00-00_init';
    }

];
