<?php

namespace Pagekit\System\Migration;

class ItemPages extends Migration
{
    public function up()
    {
        $util = $this->getUtility();
        $schema = $util->createSchema();

        if ($util->tableExists('@system_menu_item') !== false) {

            $table = $schema->getTable($this->getConnection()->replacePrefix('@system_menu_item'));
            if (!$table->hasColumn('pages')) {
                $table->addColumn('pages', 'text');
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
