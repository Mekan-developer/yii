<?php

namespace common\models;

use common\Cache;

/**
 * Region model
 *
 * @property integer $id
 * @property string  $name
 * @property integer $is_deleted
 * @property string  $created
 * @property integer $creator_id
 * @property string  $modified
 * @property integer $modifier_id
 */
class Region extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'regions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['name'], 'string', 'max' => 250],
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
        $this->name = $params['name'] ?? '';
        $dataProvider = parent::search($params, $recsOnPage);

        $query = $dataProvider->query;
        $this->name = trim($this->name);
        if ($this->name) {
            $query->andWhere(['like', 'name', $this->name]);
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
//            asort($tmp);
            return $tmp;
        }, 0, true);
    }
}