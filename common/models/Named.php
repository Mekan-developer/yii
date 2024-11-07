<?php

namespace common\models;

use common\Cache;

/**
 * Named model
 *
 * @property integer $id
 * @property string  $name
 * @property integer $is_deleted
 * @property string  $created
 * @property integer $creator_id
 * @property string  $modified
 * @property integer $modifier_id
 */
abstract class Named extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'name'         => 'Наименование',
        ];
    }

    public function __toString()
    {
        return $this->name;
    }


    /**
     * {@inheritdoc}
     */
    public function search($params, $recsOnPage = 20)
    {
        $dataProvider = parent::search($params, $recsOnPage);

        $query = $dataProvider->query;
        $this->name = trim($this->name);
        if ($this->name) {
            $query->andWhere(['like', 'name', trim($this->name)]);
        }
        return $dataProvider;
    }

    public function afterSave($insert, $changedAttributes)
    {
        Cache::dropCache(static::tableName());
        parent::afterSave($insert, $changedAttributes);
    }


    public function afterDelete()
    {
        Cache::dropCache(static::tableName());
        parent::afterDelete();
    }

    public static function getList()
    {
        return Cache::getOrSetCache(static::tableName(), 'list', function() {
            $qry = static::findActive();
            $tmp = [];
            foreach($qry->each(1) as $model) {
                $tmp[$model->id] = (string)$model;
            }
//            asort($tmp);
            usort($tmp, '\app\models\BaseActiveRecord::sortByName');
            return $tmp;
        }, 0, true);
    }

    public static function getIdByName($value)
    {
        $value = trim($value);
        if (!$value) return 0;
        $value = mb_strtolower($value);
        $list = static::getList();

        $found = 0;
        foreach ($list as $id => $val) {
            if (mb_strtolower($val) == $value) {
                $found = $id;
            }
        }
        return $found;
    }
}