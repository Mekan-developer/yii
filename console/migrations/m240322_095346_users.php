<?php

use yii\db\Migration;

/**
 * Class m240322_095346_user
 */
class m240322_095346_users extends Migration
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

        $this->createTable('users', [
            'id'            => $this->primaryKey(),
            'firstname'     => $this->string(100)->defaultValue(''),
            'lastname'      => $this->string(100)->defaultValue(''),
            'surname'       => $this->string(100)->defaultValue(''),
            'fullname'      => $this->string(255)->defaultValue(''),
            'fio'           => $this->string(255)->defaultValue(''),
            'email'         => $this->string(255)->defaultValue(''),
            'phone'         => $this->string(100)->defaultValue(''),
            'password_hash' => $this->string(60)->defaultValue(''),
            'type'          => $this->smallInteger()->notNull()->defaultValue(0),
            'auth_key'      => $this->string(32)->defaultValue(''),
            'is_deleted'    => $this->smallInteger()->notNull()->defaultValue(0),
            'created'       => $this->dateTime()->defaultValue(NULL),
            'creator_id'    => $this->integer()->notNull()->defaultValue(0),
            'modified'      => $this->dateTime()->defaultValue(NULL),
            'modifier_id'   => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $user = new \common\models\User();
        $user->firstname = 'Максим';
        $user->lastname = 'Тест';
        $user->email = 'max@mail.ru';
        $user->password  = 'test';
        $user->type = \common\models\User::USER_TYPE_ADMIN;
        $user->save();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('users');
    }
}
