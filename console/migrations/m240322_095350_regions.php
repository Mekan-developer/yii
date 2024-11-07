<?php

use yii\db\Migration;

/**
 * Class m240322_095346_user
 */
class m240322_095350_regions extends Migration
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

        $this->createTable('regions', [
            'id'            => $this->primaryKey(),
            'name'          => $this->string(250)->defaultValue(''),
            'is_deleted'    => $this->smallInteger()->notNull()->defaultValue(0),
            'created'       => $this->dateTime()->defaultValue(NULL),
            'creator_id'    => $this->integer()->notNull()->defaultValue(0),
            'modified'      => $this->dateTime()->defaultValue(NULL),
            'modifier_id'   => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $date = date('Y-m-d H:i:s');
        $this->batchInsert('regions', ['name', 'created', 'creator_id', 'modified', 'modifier_id'], [
            ['name' => 'Северо-запад', 'created' => $date, 'creator_id' => 1, 'modified' => $date, 'modifier_id' => 1],
            ['name' => 'Центр', 'created' => $date, 'creator_id' => 1, 'modified' => $date, 'modifier_id' => 1],
            ['name' => 'ЧТЗ', 'created' => $date, 'creator_id' => 1, 'modified' => $date, 'modifier_id' => 1],
            ['name' => 'ЧМЗ', 'created' => $date, 'creator_id' => 1, 'modified' => $date, 'modifier_id' => 1],
            ['name' => 'Ленинский', 'created' => $date, 'creator_id' => 1, 'modified' => $date, 'modifier_id' => 1],
            ['name' => 'Другой город', 'created' => $date, 'creator_id' => 1, 'modified' => $date, 'modifier_id' => 1],
        ]);

        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('regions');

        Yii::$app->cache->flush();
    }
}
