<?php

namespace Pagekit\Blog\Migration;

use Pagekit\Component\Migration\MigrationInterface;
use Pagekit\Framework\ApplicationAware;

class Blog extends ApplicationAware implements MigrationInterface
{
    public function up()
    {
        $util = $this('db')->getUtility();

        if ($util->tableExists('@blog_post') === false) {
            $util->createTable('@blog_post', function($table) {
                $table->addColumn('id', 'integer', array('unsigned' => true, 'length' => 10, 'autoincrement' => true));
                $table->addColumn('roles', 'simple_array', array('notnull' => false));
                $table->addColumn('slug', 'string', array('length' => 255));
                $table->addColumn('title', 'string', array('length' => 255));
                $table->addColumn('status', 'smallint');
                $table->addColumn('user_id', 'integer', array('unsigned' => true, 'length' => 10, 'default' => 0));
                $table->addColumn('date', 'datetime', array('notnull' => false));
                $table->addColumn('modified', 'datetime');
                $table->addColumn('subtitle', 'text', array('length' => 65532));
                $table->addColumn('content', 'text');
                $table->addColumn('excerpt', 'text');
                $table->addColumn('is_commentable', 'boolean', array('notnull' => false));
                $table->addColumn('num_comments', 'integer', array('default' => 0));
                $table->addColumn('last_comment_at', 'datetime', array('notnull' => false));
                $table->addColumn('data', 'json_array', array('notnull' => false));
                $table->setPrimaryKey(array('id'));
                $table->addUniqueIndex(array('slug'), 'POSTS_SLUG');
                $table->addIndex(array('title'), 'TITLE');
                $table->addIndex(array('user_id'), 'USER_ID');
            });
        }

        if ($util->tableExists('@blog_comment') === false) {
            $util->createTable('@blog_comment', function($table) {
                $table->addColumn('id', 'integer', array('unsigned' => true, 'length' => 10, 'autoincrement' => true));
                $table->addColumn('parent_id', 'integer', array('unsigned' => true, 'length' => 10));
                $table->addColumn('thread_id', 'integer', array('unsigned' => true, 'length' => 10));
                $table->addColumn('user_id', 'string', array('length' => 255));
                $table->addColumn('author', 'string', array('length' => 255));
                $table->addColumn('email', 'string', array('length' => 255));
                $table->addColumn('url', 'string', array('length' => 255));
                $table->addColumn('ip', 'string', array('length' => 255));
                $table->addColumn('created', 'datetime');
                $table->addColumn('content', 'text');
                $table->addColumn('status', 'smallint');
                $table->addColumn('previous_status', 'smallint');
                $table->addColumn('depth', 'smallint');
                $table->setPrimaryKey(array('id'));
                $table->addIndex(array('status'), 'STATUS');
                $table->addIndex(array('created'), 'CREATED');
                $table->addIndex(array('thread_id'), 'THREAD_ID');
                $table->addIndex(array('author'), 'AUTHOR');
                $table->addIndex(array('thread_id', 'status'), 'THREAD_ID_STATUS');
            });
        }
    }

    public function down()
    {
    }
}
