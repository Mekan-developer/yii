<?php

use yii\db\Migration;

/**
 * Class m240322_095346_user
 */
class m240322_095347_vars extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('vars', [
            'id'           => $this->primaryKey()->unsigned(),
            'key'          => $this->string(30)->notNull()->defaultValue(''),
            'type'         => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0),
            'value'        => $this->string(100)->notNull()->defaultValue(''),
            'values'       => $this->json(),
            'description'  => $this->string(255)->defaultValue(''),
            'is_deleted'   => $this->smallInteger()->notNull()->unsigned()->defaultValue(0),
            'created'      => $this->dateTime()->defaultValue(NULL),
            'creator_id'   => $this->integer()->notNull()->unsigned()->defaultValue(0),
            'modified'     => $this->dateTime()->defaultValue(NULL),
            'modifier_id'  => $this->integer()->notNull()->unsigned()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('idx-vars-key', 'vars', 'key');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('vars');
    }
}
