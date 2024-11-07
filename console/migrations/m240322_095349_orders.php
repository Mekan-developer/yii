<?php

use yii\db\Migration;

/**
 * Class m240322_095346_user
 */
class m240322_095349_orders extends Migration
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

        $this->createTable('orders', [
            'id'            => $this->primaryKey(),
            'number'        => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'date'          => $this->date()->defaultValue(null),
            'client'        => $this->string(250)->defaultValue(''),
            'phone'         => $this->string(250)->defaultValue(''),
            'region_id'     => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'positions'     => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'q'             => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'sum'           => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'is_deleted'    => $this->smallInteger()->notNull()->defaultValue(0),
            'created'       => $this->dateTime()->defaultValue(NULL),
            'creator_id'    => $this->integer()->notNull()->defaultValue(0),
            'modified'      => $this->dateTime()->defaultValue(NULL),
            'modifier_id'   => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createTable('orders_products', [
            'id'            => $this->primaryKey(),
            'order_id'      => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'product_id'    => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'name'          => $this->string(250)->defaultValue(''),
            'price'         => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'q'             => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'sum'           => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'is_deleted'    => $this->smallInteger()->notNull()->defaultValue(0),
            'created'       => $this->dateTime()->defaultValue(NULL),
            'creator_id'    => $this->integer()->notNull()->defaultValue(0),
            'modified'      => $this->dateTime()->defaultValue(NULL),
            'modifier_id'   => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->createIndex('idx-orders_products-order_id', 'orders_products', 'order_id');

        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('orders');
        $this->dropTable('orders_products');

        Yii::$app->cache->flush();
    }
}
