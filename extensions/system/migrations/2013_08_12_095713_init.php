<?php

namespace Pagekit\System\Migration;

use Pagekit\Component\Migration\MigrationInterface;
use Pagekit\Framework\ApplicationAware;
use Pagekit\User\Model\RoleInterface;

class Init extends ApplicationAware implements MigrationInterface
{
    public function up()
    {
        $util = $this('db')->getUtility();

        if ($util->tableExists('@system_option') === false) {
            $util->createTable('@system_option', function($table) {
                $table->addColumn('id', 'integer', array('unsigned' => true, 'length' => 10, 'autoincrement' => true));
                $table->addColumn('name', 'string', array('length' => 64, 'default' => ''));
                $table->addColumn('value', 'text');
                $table->addColumn('autoload', 'boolean', array('default' => false));
                $table->setPrimaryKey(array('id'));
                $table->addUniqueIndex(array('name'), 'OPTION_NAME');
            });
        }

        if ($util->tableExists('@system_session') === false) {
            $util->createTable('@system_session', function($table) {
                $table->addColumn('id', 'string', array('length' => 255));
                $table->addColumn('data', 'text', array('length' => 65532));
                $table->addColumn('time', 'datetime');
                $table->setPrimaryKey(array('id'));
            });
        }

        if ($util->tableExists('@system_role') === false) {
            $util->createTable('@system_role', function($table) {
                $table->addColumn('id', 'integer', array('unsigned' => true, 'length' => 10, 'autoincrement' => true));
                $table->addColumn('name', 'string', array('length' => 64));
                $table->addColumn('priority', 'integer', array('default' => 0));
                $table->addColumn('permissions', 'simple_array', array('notnull' => false));
                $table->setPrimaryKey(array('id'));
                $table->addUniqueIndex(array('name'), 'ROLE_NAME');
                $table->addIndex(array('name', 'priority'), 'ROLE_NAME_PRIORITY');
            });

            $this('db')->insert('@system_role', array('id' => RoleInterface::ROLE_ANONYMOUS, 'name' => 'Anonymous', 'priority' => 0));
            $this('db')->insert('@system_role', array('id' => RoleInterface::ROLE_AUTHENTICATED, 'name' => 'Authenticated', 'priority' => 1));
            $this('db')->insert('@system_role', array('id' => RoleInterface::ROLE_ADMINISTRATOR, 'name' => 'Administrator', 'priority' => 2));
        }

        if ($util->tableExists('@system_access_level') === false) {
            $util->createTable('@system_access_level', function($table) {
                $table->addColumn('id', 'integer', array('unsigned' => true, 'length' => 10, 'autoincrement' => true));
                $table->addColumn('name', 'string', array('length' => 64));
                $table->addColumn('priority', 'integer', array('default' => 0));
                $table->addColumn('roles', 'simple_array', array('notnull' => false));
                $table->setPrimaryKey(array('id'));
                $table->addUniqueIndex(array('name'), 'ACCESS_NAME');
            });

            $this('db')->insert('@system_access_level', array('id' => 1, 'name' => 'Public', 'priority' => 0, 'roles' => null));
            $this('db')->insert('@system_access_level', array('id' => 2, 'name' => 'Registered', 'priority' => 1, 'roles' => RoleInterface::ROLE_AUTHENTICATED));
        }

        if ($util->tableExists('@system_user') === false) {
            $util->createTable('@system_user', function($table) {
                $table->addColumn('id', 'integer', array('unsigned' => true, 'length' => 10, 'autoincrement' => true));
                $table->addColumn('name', 'string', array('length' => 255, 'default' => ''));
                $table->addColumn('username', 'string', array('length' => 150, 'default' => ''));
                $table->addColumn('email', 'string', array('length' => 100, 'default' => ''));
                $table->addColumn('password', 'string', array('length' => 255, 'default' => ''));
                $table->addColumn('url', 'string', array('length' => 100, 'default' => ''));
                $table->addColumn('status', 'smallint', array('default' => 0));
                $table->addColumn('registered', 'datetime');
                $table->addColumn('login', 'datetime', array('notnull' => false));
                $table->addColumn('access', 'datetime', array('notnull' => false));
                $table->addColumn('activation', 'string', array('length' => 255, 'notnull' => false));
                $table->addColumn('data', 'json_array', array('notnull' => false));
                $table->setPrimaryKey(array('id'));
                $table->addUniqueIndex(array('username'), 'USER_USERNAME');
                $table->addUniqueIndex(array('email'), 'USER_EMAIL');
            });
        }

        if ($util->tableExists('@system_user_role') === false) {
            $util->createTable('@system_user_role', function($table) {
                $table->addColumn('user_id', 'integer', array('unsigned' => true, 'length' => 10));
                $table->addColumn('role_id', 'integer', array('unsigned' => true, 'length' => 10));
                $table->addColumn('permissions', 'simple_array', array('notnull' => false));
                $table->setPrimaryKey(array('user_id', 'role_id'));
            });
        }

        if ($util->tableExists('@system_url_alias') === false) {
            $util->createTable('@system_url_alias', function($table) {
                $table->addColumn('id', 'integer', array('unsigned' => true, 'length' => 10, 'autoincrement' => true));
                $table->addColumn('source', 'string', array('length' => 255));
                $table->addColumn('alias', 'string', array('length' => 255));
                $table->setPrimaryKey(array('id'));
                $table->addUniqueIndex(array('alias'), 'URL_ALIAS');
                $table->addIndex(array('source'), 'URL_ALIAS_SOURCE');
            });
        }

        if ($util->tableExists('@system_menu') === false) {
            $util->createTable('@system_menu', function($table) {
                $table->addColumn('id', 'integer', array('unsigned' => true, 'length' => 10, 'autoincrement' => true));
                $table->addColumn('name', 'string', array('length' => 255));
                $table->setPrimaryKey(array('id'));
                $table->addUniqueIndex(array('name'), 'MENU_NAME');
            });
        }

        if ($util->tableExists('@system_menu_item') === false) {
            $util->createTable('@system_menu_item', function($table) {
                $table->addColumn('id', 'integer', array('unsigned' => true, 'length' => 10, 'autoincrement' => true));
                $table->addColumn('menu_id', 'integer', array('unsigned' => true, 'length' => 10));
                $table->addColumn('parent_id', 'integer', array('unsigned' => true, 'length' => 10));
                $table->addColumn('access_id', 'integer', array('unsigned' => true, 'length' => 10));
                $table->addColumn('name', 'string', array('length' => 255));
                $table->addColumn('url', 'string', array('length' => 1023));
                $table->addColumn('priority', 'integer', array('default' => 0));
                $table->addColumn('status', 'smallint');
                $table->addColumn('depth', 'smallint');
                $table->addColumn('pages', 'text');
                $table->addColumn('data', 'json_array', array('notnull' => false));
                $table->setPrimaryKey(array('id'));
            });
        }

        if ($util->tableExists('@system_widget') === false) {
            $util->createTable('@system_widget', function($table) {
                $table->addColumn('id', 'integer', array('unsigned' => true, 'length' => 10, 'autoincrement' => true));
                $table->addColumn('access_id', 'integer', array('unsigned' => true, 'length' => 10));
                $table->addColumn('type', 'string', array('length' => 255));
                $table->addColumn('title', 'string', array('length' => 255));
                $table->addColumn('position', 'string', array('length' => 255));
                $table->addColumn('priority', 'integer', array('default' => 0));
                $table->addColumn('status', 'boolean');
                $table->addColumn('pages', 'text');
                $table->addColumn('menu_items', 'simple_array', array('notnull' => false));
                $table->addColumn('data', 'json_array', array('notnull' => false));
                $table->setPrimaryKey(array('id'));
            });
        }

        if ($util->tableExists('@page_page') === false) {
            $util->createTable('@page_page', function($table) {
                $table->addColumn('id', 'integer', array('unsigned' => true, 'length' => 10, 'autoincrement' => true));
                $table->addColumn('access_id', 'integer', array('unsigned' => true, 'length' => 10));
                $table->addColumn('slug', 'string', array('length' => 255));
                $table->addColumn('title', 'string', array('length' => 255));
                $table->addColumn('status', 'smallint');
                $table->addColumn('content', 'text');
                $table->addColumn('data', 'json_array', array('notnull' => false));
                $table->setPrimaryKey(array('id'));
                $table->addUniqueIndex(array('slug'), 'PAGES_SLUG');
            });
        }
    }

    public function down()
    {
    }
}
