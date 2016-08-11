<?php

return [

    'install' => function ($app) {

        $db = $app['db'];
        $util = $db->getUtility();

        if ($util->tableExists('@system_auth') === false) {
            $util->createTable('@system_auth', function ($table) {
                $table->addColumn('id', 'string', ['length' => 255]);
                $table->addColumn('user_id', 'integer', ['unsigned' => true, 'length' => 10, 'default' => 0]);
                $table->addColumn('access', 'datetime', ['notnull' => false]);
                $table->addColumn('status', 'smallint');
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
            });
        }

        if ($util->tableExists('@system_config') === false) {
            $util->createTable('@system_config', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('name', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('value', 'text');
                $table->setPrimaryKey(['id']);
                $table->addUniqueIndex(['name'], '@SYSTEM_CONFIG_NAME');
            });
        }

        if ($util->tableExists('@system_node') === false) {
            $util->createTable('@system_node', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('parent_id', 'integer', ['unsigned' => true, 'length' => 10, 'default' => 0]);
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
            $util->createTable('@system_page', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('title', 'string', ['length' => 255]);
                $table->addColumn('content', 'text');
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
            });
        }

        if ($util->tableExists('@system_role') === false) {
            $util->createTable('@system_role', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('name', 'string', ['length' => 255]);
                $table->addColumn('priority', 'integer', ['default' => 0]);
                $table->addColumn('permissions', 'simple_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
                $table->addUniqueIndex(['name'], '@SYSTEM_ROLE_NAME');
                $table->addIndex(['name', 'priority'], '@SYSTEM_ROLE_NAME_PRIORITY');
            });

            $db->insert('@system_role', ['id' => 1, 'name' => 'Anonymous', 'priority' => 0]);
            $db->insert('@system_role', ['id' => 2, 'name' => 'Authenticated', 'priority' => 1, 'permissions' => 'blog: post comments']);
            $db->insert('@system_role', ['id' => 3, 'name' => 'Administrator', 'priority' => 2]);
        }

        if ($util->tableExists('@system_session') === false) {
            $util->createTable('@system_session', function ($table) {
                $table->addColumn('id', 'string', ['length' => 255]);
                $table->addColumn('time', 'datetime');
                $table->addColumn('data', 'text', ['length' => 65532]);
                $table->setPrimaryKey(['id']);
            });
        }

        if ($util->tableExists('@system_user') === false) {
            $util->createTable('@system_user', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('name', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('username', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('email', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('password', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('url', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('status', 'smallint', ['default' => 0]);
                $table->addColumn('registered', 'datetime');
                $table->addColumn('login', 'datetime', ['notnull' => false]);
                $table->addColumn('activation', 'string', ['length' => 255, 'notnull' => false]);
                $table->addColumn('roles', 'simple_array', ['notnull' => false]);
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
                $table->addUniqueIndex(['username'], '@SYSTEM_USER_USERNAME');
                $table->addUniqueIndex(['email'], '@SYSTEM_USER_EMAIL');
            });
        }

        if ($util->tableExists('@system_widget') === false) {
            $util->createTable('@system_widget', function ($table) {
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

        $app['config']->set('system/dashboard', [
            '55dda578e93b5' => ['type' => 'location', 'column' => 1, 'idx' => 0, 'units' => 'metric', 'id' => '55dda578e93b5', 'uid' => 2911298, 'city' => 'Hamburg', 'country' => 'DE', 'coords' => ['lon' => 10, 'lat' => 53.549999]],
            '55dda581d5781' => ['type' => 'feed', 'column' => 2, 'idx' => 0, 'count' => 5, 'content' => '1', 'id' => '55dda581d5781', 'title' => 'Pagekit News', 'url' => 'http://pagekit.com/blog/feed'],
            '55dda6e3dd661' => ['type' => 'user', 'column' => 0, 'idx' => 100, 'show' => 'registered', 'display' => 'thumbnail', 'total' => '1', 'count' => 12, 'id' => '55dda6e3dd661']
        ]);

        $app['config']->set('system/site', [
            'menus' => ['main' => ['id' => 'main', 'label' => 'Main']]
        ]);

    },

    'updates' => [

        '0.11.3' => function ($app) {

            $db = $app['db'];
            $util = $db->getUtility();

            foreach (['@system_auth', '@system_config', '@system_node', '@system_page', '@system_role', '@system_session', '@system_user', '@system_widget'] as $name) {
                $table = $util->getTable($name);

                foreach ($table->getIndexes() as $name => $index) {
                    if ($name !== 'primary') {
                        $table->renameIndex($index->getName(), $app['db']->getPrefix() . $index->getName());
                    }
                }

                if ($app['db']->getDatabasePlatform()->getName() === 'sqlite') {
                    foreach ($table->getColumns() as $column) {
                        if (in_array($column->getType()->getName(), ['string', 'text'])) {
                            $column->setOptions(['customSchemaOptions' => ['collation' => 'NOCASE']]);
                        }
                    }
                }
            }

            $util->migrate();
        },

        '1.0.7' => function ($app) {

            $dashboard = $app->module('system/dashboard');
            $widgets = $dashboard->getWidgets();

            $ids = array_filter(array_keys($widgets), function ($id) use ($widgets){
                return $id == $widgets[$id]['id'];
            });

            $dashboard->saveWidgets(array_intersect_key($widgets, array_flip($ids)));

        }

    ]

];
