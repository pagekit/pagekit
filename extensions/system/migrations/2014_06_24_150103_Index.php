<?php

namespace Pagekit\System\Migration;

class Index extends Migration
{
    public function up()
    {
        $util = $this->getUtility();
        $schema = $util->createSchema();

        if ($util->tableExists('@system_widget') !== false) {

            $table = $schema->getTable($this->getConnection()->replacePrefix('@system_widget'));

            $table->addIndex(array('status', 'priority'), 'WIDGET_STATUS_PRIORITY');

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
