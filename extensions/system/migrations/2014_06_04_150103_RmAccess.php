<?php

namespace Pagekit\System\Migration;

class RmAccess extends Migration
{
    public function up()
    {
        $util = $this->getUtility();
        $schema = $util->createSchema();

        foreach (array('@system_menu_item', '@system_widget', '@page_page') as $name) {
            if ($util->tableExists($name) !== false) {

                $table = $schema->getTable($this->getConnection()->replacePrefix($name));

                if (!$table->hasColumn('roles')) {
                    $table->addColumn('roles', 'simple_array', array('notnull' => false));
                }

                if ($table->hasColumn('access_id')) {
                    $table->dropColumn('access_id');
                }
            }
        }

        if ($queries = $schema->getMigrateFromSql($util->createSchema(), $util->getDatabasePlatform())) {
            foreach ($queries as $query) {
                $this->getConnection()->executeQuery($query);
            }
        }

        if ($util->tableExists('@system_access_level') !== false) {
            $util->dropTable('@system_access_level');
        }
    }

    public function down()
    {
    }
}
