<?php

namespace common\models;

use common\Cache;
use common\validators\RequiredIntegerValidator;
use Yii;

/**
 * Variable model
 *
 * @property integer $id
 * @property string  $key
 * @property integer $type
 * @property string  $value
 * @property mixed   $values
 * @property string  $description
 * @property integer $is_deleted
 * @property string  $created
 * @property integer $creator_id
 * @property string  $modified
 * @property integer $modifier_id
 */
class Vars extends BaseActiveRecord
{
    const TYPE_STRING    = 1;
    const TYPE_BOOLEAN   = 2;
    const TYPE_INTEGER   = 3;
    const TYPE_DECIMAL   = 4;
    const TYPE_LIST      = 5;

    public $editValues = null;

    public static function getVarTypes() {
        return [
            static::TYPE_STRING  => 'Строка',
            static::TYPE_BOOLEAN => 'Boolean',
            static::TYPE_INTEGER => 'Целое число',
            static::TYPE_DECIMAL => 'Десятичное число',
            static::TYPE_LIST    => 'Список',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vars';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key', 'description'], 'required'],
            [['type'], RequiredIntegerValidator::class],
            [['type'], 'integer'],
            [['key'], 'string', 'max' => 30],
            [['value'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 100],
            [['editValues'], 'safe'],
            ['key', 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'key'         => 'Key',
            'description' => 'Description',
            'values'      => 'Values',
            'value'       => 'Value',
            'created'     => 'Created',
            'creator'     => 'Created',
            'creator_id'  => 'Created',
            'modified'    => 'Modified',
            'modifier'    => 'Modified',
            'modifier_id' => 'Modified',
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->editValues !== null) {
            $values = [];
            if (is_array($this->editValues)) {
                foreach($this->editValues as $value) {
                    if (!isset($value['value']) || empty($value['name'])) continue;
                    $values[$value['value']] = $value['name'];
                }
            }
            $this->values = json_encode($values);
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        static::cache();
    }
    public function afterFind()
    {
        parent::afterFind();
        $this->loadEditValues();
    }

    public function loadEditValues()
    {
        $values = $this->values;
        if (!is_array($values)) {
            try {
                $values = (array)json_decode($values, true);
            } catch (\Exception $x) {

            }
        }
        $this->editValues = [];
        foreach($values as $key => $value) {
            $this->editValues[] = [
                'value' => $key,
                'name' => $value,
            ];
        }
    }

    public static function cache()
    {
        // Prepare data
        $varModels = static::findActive()->all();
        $vars = [];

        // Set Global values
        foreach($varModels as $varModel) {
            if ($varModel->type == Vars::TYPE_DECIMAL) $varModel->value = rtrim($varModel->value, '0');
            $vars[$varModel->key] = $varModel->value;
        }

        // Save values into a temp file
        Cache::setCache('vars', '', $vars, true);
    }

    public static function clear()
    {
        Cache::dropCache('vars');
    }

    public static function getString($key, $value) {
        return static::getVar($key, static::TYPE_STRING, $value);
    }

    public static function getBoolean($key, $value) {
        return static::getVar($key, static::TYPE_BOOLEAN, $value) ? true : false;
    }

    public static function getInteger($key, $value) {
        return (int)static::getVar($key, static::TYPE_INTEGER, $value);
    }

    public static function getDecimal($key, $value) {
        return static::getVar($key, static::TYPE_DECIMAL, $value);
    }

    public static function getVar($key, $type, $default)
    {
        // Load cache
        $vars = (array)Cache::getCache('vars', '', 0 ,true);

        if (!isset($vars[$key])) {
            // Create new variable
            $value = $default;
            $exists = Vars::findActive()->andWhere(['key' => $key, 'type' => $type])->one();
            if (!$exists) {
                $exists = new Vars();
                $exists->key = $key;
                $exists->value = (string)$value;
                $exists->type = $type;
                $exists->save();
            }

            // Resave cache
            static::cache();
        } else {
            // Get value
            $value = $vars[$key];
        }

        // Format value
        switch ($type) {
            case static::TYPE_BOOLEAN: $value = $value ? '1' : '0'; break;
            case static::TYPE_INTEGER: $value = (string)((int)$value); break;
            case static::TYPE_DECIMAL: $value = number_format($value, 6, '.', ''); break;
            default:
                $value = (string)$value;
        }

        return $value;
    }
}