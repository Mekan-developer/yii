<?php

namespace common\models;

use common\Cache;
use common\validators\RequiredIntegerValidator;

/**
 * Product model
 *
 * @property integer $id
 * @property string  $name
 * @property integer $q
 * @property integer $price
 * @property integer $is_deleted
 * @property string  $created
 * @property integer $creator_id
 * @property string  $modified
 * @property integer $modifier_id
 */
class Product extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['name'], 'string', 'max' => 250],
            [['price'], RequiredIntegerValidator::class, 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['price', 'q'], 'integer'],
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
            'q'            => 'Количество',
            'price'        => 'Цена',
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
            asort($tmp);
            return $tmp;
        }, 0, true);
    }

    public static function getDataList()
    {
        $qry = static::findActive()->andWhere(['>', 'q', 0]);
        $names = $data = [];
        foreach($qry->each(1) as $model) {
            $names[$model->id] = (string)$model;
            $data[$model->id] = ['price' => $model->price, 'q' => $model->q];
        }
        asort($names);
        return [$names, $data];
    }

    public static function getDataListFront()
    {
        $qry = static::findActive()->andWhere(['>', 'q', 0]);
        $names = $data = [];
        foreach($qry->each(1) as $model) {
            $names[$model->id] = (string)$model;
            $data[$model->id] = $model->attributes;
        }
        asort($names);
        return [$names, $data];
    }
}