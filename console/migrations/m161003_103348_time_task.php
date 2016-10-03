<?php

use yii\db\Migration;

class m161003_103348_time_task extends Migration
{
    public function up()
    {
        $this->createTable('time_task', [
            'id' => $this->primaryKey(),
            'begin' => $this->dateTime()->notNull(),
            'end' => $this->dateTime()->notNull(),
        ]);


    }

    public function down()
    {
        $this->dropTable('time_task');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
