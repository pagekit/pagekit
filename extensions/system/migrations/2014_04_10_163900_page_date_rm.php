<?php

namespace Pagekit\System\Migration;

class PageDateRm extends Migration
{
    public function up()
    {
        $util = $this->getUtility();
        $schema = $util->createSchema();

        if ($util->tableExists('@page_page') !== false) {

            $table = $schema->getTable($this->getConnection()->replacePrefix('@page_page'));
            if ($table->hasColumn('date')) {
                $table->dropColumn('date');
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
