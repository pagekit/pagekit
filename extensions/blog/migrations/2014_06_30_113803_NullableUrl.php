<?php

namespace Pagekit\Blog\Migration;

use Pagekit\System\Migration\Migration;

class NullableUrl extends Migration
{
    public function up()
    {
        $util = $this->getUtility();
        $schema = $util->createSchema();

        if ($util->tableExists('@blog_comment') !== false) {

            $table = $schema->getTable($this->getConnection()->replacePrefix('@blog_comment'));

            if ($table->hasColumn('url')) {
                $table->changeColumn('url', ['length' => 255, 'notnull' => false]);
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
