<?php

namespace Pagekit\System\Migration;

class PageDate extends Migration
{
    public function up()
    {
        $util = $this->getUtility();
        $schema = $util->createSchema();

        if ($util->tableExists('@page_page') !== false) {

            $table = $schema->getTable($this->getConnection()->replacePrefix('@page_page'));
            if (!$table->hasColumn('date')) {
                $table->addColumn('date', 'datetime', array('notnull' => false));
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
