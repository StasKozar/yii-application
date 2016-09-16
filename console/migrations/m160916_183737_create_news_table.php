<?php

use yii\db\Migration;

/**
 * Handles the creation for table `news`.
 */
class m160916_183737_create_news_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('news', [
            'id' => $this->primaryKey(),
            'article' => $this->string(255)->notNull(),
            'intro_text' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'author' => $this->string(255)->notNull(),
            'update_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT TIMESTAMP'),
            'create_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT TIMESTAMP'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('news');
    }
}
