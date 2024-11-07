<?php

use yii\db\Migration;

/**
 * Class m240322_095346_user
 */
class m240322_095348_products extends Migration
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

        $this->createTable('products', [
            'id'            => $this->primaryKey(),
            'name'          => $this->string(250)->defaultValue(''),
            'q'             => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'price'         => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'is_deleted'    => $this->smallInteger()->notNull()->defaultValue(0),
            'created'       => $this->dateTime()->defaultValue(NULL),
            'creator_id'    => $this->integer()->notNull()->defaultValue(0),
            'modified'      => $this->dateTime()->defaultValue(NULL),
            'modifier_id'   => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('products');

        Yii::$app->cache->flush();
    }
}
