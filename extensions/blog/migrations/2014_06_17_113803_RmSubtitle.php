<?php

namespace Pagekit\Blog\Migration;

use Pagekit\System\Migration\Migration;

class RmAccess extends Migration
{
    public function up()
    {
        $util = $this->getUtility();
        $schema = $util->createSchema();

        if ($util->tableExists('@blog_post') !== false) {

            $table = $schema->getTable($this->getConnection()->replacePrefix('@blog_post'));

            if ($table->hasColumn('subtitle')) {
                $table->dropColumn('subtitle');
            }

            if ($queries = $schema->getMigrateFromSql($util->createSchema(), $util->getDatabasePlatform())) {
                foreach ($queries as $query) {
                    $this->getConnection()->executeQuery($query);
                }
            }
        }
    }

    public function down()
    {
    }
}
