<?php

namespace Pagekit\System\Migration;

use Pagekit\Component\Migration\MigrationInterface;
use Pagekit\Framework\ApplicationAware;

class PageDateRm extends ApplicationAware implements MigrationInterface
{
    public function up()
    {
        $util = $this('db')->getUtility();
        $schema = $util->createSchema();

        if ($util->tableExists('@page_page') !== false) {

            $table = $schema->getTable($this('db')->replacePrefix('@page_page'));
            if ($table->hasColumn('date')) {
                $table->dropColumn('date');
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
