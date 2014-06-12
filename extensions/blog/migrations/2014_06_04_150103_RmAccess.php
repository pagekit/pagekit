<?php

namespace Pagekit\Blog\Migration;

use Pagekit\Component\Migration\MigrationInterface;
use Pagekit\Framework\ApplicationAware;

class RmAccess extends ApplicationAware implements MigrationInterface
{
    public function up()
    {
        $util = $this('db')->getUtility();
        $schema = $util->createSchema();

        if ($util->tableExists('@blog_post') !== false) {

            $table = $schema->getTable($this('db')->replacePrefix('@blog_post'));

            if (!$table->hasColumn('roles')) {
                $table->addColumn('roles', 'simple_array', array('notnull' => false));
            }

            if ($table->hasColumn('access_id')) {
                $table->dropColumn('access_id');
            }

            if ($queries = $schema->getMigrateFromSql($util->createSchema(), $util->getDatabasePlatform())) {
                foreach ($queries as $query) {
                    $this('db')->executeQuery($query);
                }
            }
        }
    }

    public function down()
    {
    }
}
