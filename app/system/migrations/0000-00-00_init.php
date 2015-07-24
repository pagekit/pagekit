<?php

return [

    'up' => function() use ($app) {

        $db = $app['db'];
        $util = $app['db']->getUtility();

        if ($util->tableExists('@system_config') === false) {
            $util->createTable('@system_config', function($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('name', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('value', 'text');
                $table->setPrimaryKey(['id']);
                $table->addUniqueIndex(['name'], 'SYSTEM_CONFIG_NAME');
            });
        }

        if ($util->tableExists('@system_node') === false) {
            $util->createTable('@system_node', function($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('parent_id', 'integer', ['unsigned' => true, 'length' => 10]);
                $table->addColumn('priority', 'integer', ['default' => 0]);
                $table->addColumn('status', 'smallint');
                $table->addColumn('title', 'string', ['length' => 255]);
                $table->addColumn('slug', 'string', ['length' => 255]);
                $table->addColumn('path', 'string', ['length' => 1023]);
                $table->addColumn('link', 'string', ['length' => 255]);
                $table->addColumn('type', 'string', ['length' => 255]);
                $table->addColumn('menu', 'string', ['length' => 255]);
                $table->addColumn('roles', 'simple_array', ['notnull' => false]);
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
            });
        }

        if ($util->tableExists('@system_page') === false) {
            $util->createTable('@system_page', function($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('title', 'string', ['length' => 255]);
                $table->addColumn('content', 'text');
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
            });
        }

        if ($util->tableExists('@system_role') === false) {
            $util->createTable('@system_role', function($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('name', 'string', ['length' => 255]);
                $table->addColumn('priority', 'integer', ['default' => 0]);
                $table->addColumn('permissions', 'simple_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
                $table->addUniqueIndex(['name'], 'SYSTEM_ROLE_NAME');
                $table->addIndex(['name', 'priority'], 'SYSTEM_ROLE_NAME_PRIORITY');
            });

            $db->insert('@system_role', ['id' => 1, 'name' => 'Anonymous', 'priority' => 0]);
            $db->insert('@system_role', ['id' => 2, 'name' => 'Authenticated', 'priority' => 1]);
            $db->insert('@system_role', ['id' => 3, 'name' => 'Administrator', 'priority' => 2]);
        }

        if ($util->tableExists('@system_session') === false) {
            $util->createTable('@system_session', function($table) {
                $table->addColumn('id', 'string', ['length' => 255]);
                $table->addColumn('data', 'text', ['length' => 65532]);
                $table->addColumn('time', 'datetime');
                $table->setPrimaryKey(['id']);
            });
        }

        if ($util->tableExists('@system_user') === false) {
            $util->createTable('@system_user', function($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('name', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('username', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('email', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('password', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('url', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('status', 'smallint', ['default' => 0]);
                $table->addColumn('registered', 'datetime');
                $table->addColumn('login', 'datetime', ['notnull' => false]);
                $table->addColumn('access', 'datetime', ['notnull' => false]);
                $table->addColumn('activation', 'string', ['length' => 255, 'notnull' => false]);
                $table->addColumn('roles', 'simple_array', ['notnull' => false]);
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
                $table->addUniqueIndex(['username'], 'SYSTEM_USER_USERNAME');
                $table->addUniqueIndex(['email'], 'SYSTEM_USER_EMAIL');
            });
        }

        if ($util->tableExists('@system_widget') === false) {
            $util->createTable('@system_widget', function($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('title', 'string', ['length' => 255]);
                $table->addColumn('type', 'string', ['length' => 255]);
                $table->addColumn('status', 'smallint');
                $table->addColumn('nodes', 'simple_array', ['notnull' => false]);
                $table->addColumn('roles', 'simple_array', ['notnull' => false]);
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
            });
        }

        // TODO use data from package.json
        // skip migrations and return latest version
        return '2014-08-28_0.8.6';
    }

];
