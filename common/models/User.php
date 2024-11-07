<?php

namespace common\models;

use common\assets\PlaceholderAsset;
use common\Cache;
use common\validators\RequiredIntegerValidator;
use common\widgets\Flashes;
use Yii;

/**
 * User model
 *
 * @property integer $id
 * @property string  $firstname
 * @property string  $lastname
 * @property string  $surname
 * @property string  $fullname
 * @property string  $fio
 * @property string  $email
 * @property string  $phone
 * @property string  $password_hash
 * @property integer $type
 * @property string  $auth_key
 * @property integer $is_deleted
 * @property string  $created
 * @property integer $creator_id
 * @property string  $modified
 * @property integer $modifier_id
 *
 * @property string  $typeName
 */
class User extends BaseActiveRecord
{
    public $search = '';

    // holds the password confirmation word
    public $password_repeat = '';

    // holds new password
    public $password_new = '';

    const USER_TYPE_ADMIN = 1;
    const USER_TYPE_USER = 2;

    public static function listTypes()
    {
        return [
            static::USER_TYPE_ADMIN   => 'Администратор',
            static::USER_TYPE_USER    => 'Пользователь',
        ];
    }

    public function getTypeName()
    {
        $list = static::listTypes();
        return $list[$this->type] ?? $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstname', 'lastname', 'email'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['firstname', 'lastname', 'surname', 'phone'], 'string', 'max' => 100],
            [['email'], 'string', 'max' => 255],
            [['email'], 'email', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['type'], RequiredIntegerValidator::class, 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['type'], 'integer'],


            [['password_new', 'password_repeat'], 'required', 'on' => [static::SCENARIO_INSERT]],
            [['password_new', 'password_repeat'], 'string', 'min' => 6, 'max' => 40],
            [['password_new'], 'compare', 'compareAttribute' => 'password_repeat'],

            [['search'], 'string', 'on' => [static::SCENARIO_SEARCH]],
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if(!$this->checkEmail($this->isNewRecord ? 0 : $this->id)) {
                Flashes::setError('Пользователь с таким адресом почты уже существует!');
                return false;
            }

            if(!empty($this->password_new) && !empty($this->password_repeat) && $this->password_new == $this->password_repeat) {
                $this->setPassword($this->password_new);
                $this->auth_key = $this->newAuthKey($this->isNewRecord ? 0 : $this->id); // Cookie-based login
            }

            // Full name
            $tmp = [];
            $tmp[] = trim($this->lastname);
            if ($this->firstname) $tmp[] = trim($this->firstname);
            if ($this->surname) $tmp[] = trim($this->surname);
            $this->fullname = implode(' ', $tmp);

            // FIO
            $tmp = [];
            $tmp[] = trim($this->lastname);
            if ($this->firstname) $tmp[] = mb_substr(trim($this->firstname), 0, 1) . '.';
            if ($this->surname) $tmp[] = mb_substr(trim($this->surname), 0, 1) . '.';
            $this->fio = implode(' ', $tmp);

            return true;
        }
        return false;
    }

    protected function checkEmail($exceptId)
    {
        if(empty($this->email)) return true;

        $models = static::find()->where("`is_deleted` = 0 AND `email` = '" . $this->email . "' AND `id` != " . $exceptId)->all();
        return (is_array($models) && sizeof($models) == 0);
    }

    public function getAvatar()
    {
        $bundle = PlaceholderAsset::register(Yii::$app->view);
        return $bundle->baseUrl . '/no-profile-picture1.jpg';
    }

    protected function newAuthKey($exceptId)
    {
        for($i = 0; $i < 10; $i++)
        {
            $str = \Yii::$app->security->generateRandomString();
            $models = static::find()->where("`auth_key` = '" . $str . "' AND `id` != " . $exceptId)->all();
            if(!is_array($models) || sizeof($models) == 0) return $str;
        }
        return \Yii::$app->security->generateRandomString();
    }

    /**
     * Generates auth key and sets it to the model
     */
    public function setupAuthKey()
    {
        $this->auth_key = $this->newAuthKey($this->isNewRecord ? 0 : $this->id);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'firstname'    => 'Имя',
            'lastname'     => 'Фамилия',
            'surname'      => 'Отчество',
            'position'     => 'Должность',
            'fullname'     => 'Полное имя',
            'fio'          => 'ФИО',
            'email'        => 'Адрес эл. почты',
            'phone'        => 'Номер телефона',
            'type'         => 'Тип',
            'typeName'     => 'Тип',
            'password_new'  => 'Новый пароль',
            'password_repeat' => 'Повтор пароля',
            'search'        => 'Поиск',
        ];
    }

    public function __toString()
    {
        return $this->fio;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @inheritDoc
     */
    public function actionAllowed($name)
    {
        if ($name == 'sign-in') return (Yii::$app->user->id == 1);
        if ($this->id == 1 && in_array($name, ['delete'])) return false;
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function search($params, $recsOnPage = 20)
    {
        $dataProvider = parent::search($params, $recsOnPage);

        $query = $dataProvider->query;
        $this->search = trim($this->search);
        if ($this->search) {
            $or = [
                'or',
                ['like', 'firstname', $this->search],
                ['like', 'lastname', $this->search],
                ['like', 'surname', $this->search],
                ['like', 'phone', $this->search],
                ['like', 'email', $this->search],
            ];
            if (is_numeric($this->search)) {
                $or[] = ['id' => $this->search];
            }
            $query->andWhere($or);
        }

        // Return data provider
        return $dataProvider;
    }

    public function afterSave($insert, $changedAttributes)
    {
        Cache::dropCache(static::tableName());
        return parent::afterSave($insert, $changedAttributes);
    }


    public function afterDelete()
    {
        Cache::dropCache(static::tableName());
        return parent::afterDelete();
    }

    public static function getList()
    {
        return Cache::getOrSetCache(static::tableName(), 'list', function() {
            $qry = static::findActive();
            $tmp = [];
            foreach($qry->each(1) as $model) {
                $tmp[$model->id] = (string)$model;
            }
            asort($tmp);
            return $tmp;
        }, 0, true);
    }
}