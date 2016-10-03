<?php

use yii\db\Migration;

class m161003_121555_work_schedule extends Migration
{
    public function up()
    {
        $this->createTable('work_schedule', [
            'day' => $this->integer()->notNull(),
            'begin' => $this->integer()->notNull(),
            'end' => $this->integer()->notNull(),
        ]);

        $this->insert('work_schedule', [
            'day' => 1,
            'begin' => 8*60*60,
            'end' => 17*60*60
        ]);
        $this->insert('work_schedule', [
            'day' => 2,
            'begin' => 8*60*60,
            'end' => 17*60*60
        ]);
        $this->insert('work_schedule', [
            'day' => 4,
            'begin' => 8*60*60,
            'end' => 17*60*60
        ]);
        $this->insert('work_schedule', [
            'day' => 5,
            'begin' => 8*60*60,
            'end' => 17*60*60
        ]);

    }

    public function down()
    {
        $this->dropTable('work_schedule');
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
