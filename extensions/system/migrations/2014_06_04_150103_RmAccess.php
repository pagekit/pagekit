<?php

namespace Pagekit\System\Migration;

use Pagekit\Component\Migration\MigrationInterface;
use Pagekit\Framework\ApplicationAware;

class RmAccess extends ApplicationAware implements MigrationInterface
{
    public function up()
    {
        $util = $this('db')->getUtility();
        $schema = $util->createSchema();

        foreach (array('@system_menu_item', '@system_widget', '@page_page') as $name) {
            if ($util->tableExists($name) !== false) {

                $table = $schema->getTable($this('db')->replacePrefix($name));

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
                $this('db')->executeQuery($query);
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
