<?php

namespace Pagekit\System\Migration;

use Pagekit\Component\Migration\MigrationInterface;
use Pagekit\Framework\ApplicationAware;

class ItemPages extends ApplicationAware implements MigrationInterface
{
    public function up()
    {
        $util = $this('db')->getUtility();
        $schema = $util->createSchema();

        if ($util->tableExists('@system_menu_item') !== false) {

            $table = $schema->getTable($this('db')->replacePrefix('@system_menu_item'));
            if (!$table->hasColumn('pages')) {
                $table->addColumn('pages', 'text');
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
