<?php

namespace Pagekit\Hello\Migration;

use Pagekit\Component\Migration\MigrationInterface;
use Pagekit\Framework\ApplicationAware;

class HelloInit extends ApplicationAware implements MigrationInterface
{
    public function up()
    {
        $util = $this('db')->getUtility();

        if ($util->tableExists('@hello_greetings') === false) {
            $util->createTable('@hello_greetings', function($table) {
                $table->addColumn('id', 'integer', array('unsigned' => true, 'length' => 10, 'autoincrement' => true));
                $table->addColumn('name', 'string', array('length' => 255, 'default' => ''));
                $table->setPrimaryKey(array('id'));
            });
        }
    }

    public function down()
    {

    }
}