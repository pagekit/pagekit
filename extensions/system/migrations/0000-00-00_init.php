<?php

use Pagekit\User\Model\RoleInterface;

return [

    'up' => function() use ($app) {

        $db = $app['db'];
        $util = $app['db']->getUtility();

        if ($util->tableExists('@system_menu') === false) {
            $util->createTable('@system_menu', function($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('name', 'string', ['length' => 255]);
                $table->setPrimaryKey(['id']);
                $table->addUniqueIndex(['name'], 'SYSTEM_MENU_NAME');
            });
        }

        if ($util->tableExists('@system_menu_item') === false) {
            $util->createTable('@system_menu_item', function($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('menu_id', 'integer', ['unsigned' => true, 'length' => 10]);
                $table->addColumn('parent_id', 'integer', ['unsigned' => true, 'length' => 10]);
                $table->addColumn('roles', 'simple_array', ['notnull' => false]);
                $table->addColumn('name', 'string', ['length' => 255]);
                $table->addColumn('url', 'string', ['length' => 1023]);
                $table->addColumn('priority', 'integer', ['default' => 0]);
                $table->addColumn('status', 'smallint');
                $table->addColumn('depth', 'smallint');
                $table->addColumn('pages', 'text');
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
            });
        }

        if ($util->tableExists('@system_option') === false) {
            $util->createTable('@system_option', function($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('name', 'string', ['length' => 64, 'default' => '']);
                $table->addColumn('value', 'text');
                $table->addColumn('autoload', 'boolean', ['default' => false]);
                $table->setPrimaryKey(['id']);
                $table->addUniqueIndex(['name'], 'SYSTEM_OPTION_NAME');
            });
        }

        if ($util->tableExists('@system_role') === false) {
            $util->createTable('@system_role', function($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('name', 'string', ['length' => 64]);
                $table->addColumn('priority', 'integer', ['default' => 0]);
                $table->addColumn('permissions', 'simple_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
                $table->addUniqueIndex(['name'], 'SYSTEM_ROLE_NAME');
                $table->addIndex(['name', 'priority'], 'SYSTEM_ROLE_NAME_PRIORITY');
            });

            $db->insert('@system_role', ['id' => RoleInterface::ROLE_ANONYMOUS, 'name' => 'Anonymous', 'priority' => 0]);
            $db->insert('@system_role', ['id' => RoleInterface::ROLE_AUTHENTICATED, 'name' => 'Authenticated', 'priority' => 1]);
            $db->insert('@system_role', ['id' => RoleInterface::ROLE_ADMINISTRATOR, 'name' => 'Administrator', 'priority' => 2]);
        }

        if ($util->tableExists('@system_session') === false) {
            $util->createTable('@system_session', function($table) {
                $table->addColumn('id', 'string', ['length' => 255]);
                $table->addColumn('data', 'text', ['length' => 65532]);
                $table->addColumn('time', 'datetime');
                $table->setPrimaryKey(['id']);
            });
        }

        if ($util->tableExists('@system_url_alias') === false) {
            $util->createTable('@system_url_alias', function($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('source', 'string', ['length' => 255]);
                $table->addColumn('alias', 'string', ['length' => 255]);
                $table->setPrimaryKey(['id']);
                $table->addUniqueIndex(['alias'], 'SYSTEM_URL_ALIAS');
                $table->addIndex(['source'], 'SYSTEM_URL_ALIAS_SOURCE');
            });
        }

        if ($util->tableExists('@system_user') === false) {
            $util->createTable('@system_user', function($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('name', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('username', 'string', ['length' => 150, 'default' => '']);
                $table->addColumn('email', 'string', ['length' => 100, 'default' => '']);
                $table->addColumn('password', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('url', 'string', ['length' => 100, 'default' => '']);
                $table->addColumn('status', 'smallint', ['default' => 0]);
                $table->addColumn('registered', 'datetime');
                $table->addColumn('login', 'datetime', ['notnull' => false]);
                $table->addColumn('access', 'datetime', ['notnull' => false]);
                $table->addColumn('activation', 'string', ['length' => 255, 'notnull' => false]);
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
                $table->addUniqueIndex(['username'], 'SYSTEM_USER_USERNAME');
                $table->addUniqueIndex(['email'], 'SYSTEM_USER_EMAIL');
            });
        }

        if ($util->tableExists('@system_user_role') === false) {
            $util->createTable('@system_user_role', function($table) {
                $table->addColumn('user_id', 'integer', ['unsigned' => true, 'length' => 10]);
                $table->addColumn('role_id', 'integer', ['unsigned' => true, 'length' => 10]);
                $table->setPrimaryKey(['user_id', 'role_id']);
            });
        }

        if ($util->tableExists('@system_widget') === false) {
            $util->createTable('@system_widget', function($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('roles', 'simple_array', ['notnull' => false]);
                $table->addColumn('type', 'string', ['length' => 255]);
                $table->addColumn('title', 'string', ['length' => 255]);
                $table->addColumn('position', 'string', ['length' => 255]);
                $table->addColumn('priority', 'integer', ['default' => 0]);
                $table->addColumn('status', 'boolean');
                $table->addColumn('pages', 'text');
                $table->addColumn('menu_items', 'simple_array', ['notnull' => false]);
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
                $table->addIndex(['status', 'priority'], 'SYSTEM_WIDGET_STATUS_PRIORITY');
            });
        }

        // skip migrations and return latest version
        return '2014-08-28_0.8.6';
    }

];
