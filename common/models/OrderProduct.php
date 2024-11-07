<?php

namespace common\models;

use common\validators\RequiredIntegerValidator;

/**
 * Order Product model
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $product_id
 * @property string  $name
 * @property integer $price
 * @property integer $q
 * @property integer $sum
 * @property integer $is_deleted
 * @property string  $created
 * @property integer $creator_id
 * @property string  $modified
 * @property integer $modifier_id
 *
 * @property Order   $order
 * @property Product $product
 */
class OrderProduct extends BaseActiveRecord
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders_products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'order_id', 'price', 'q', 'sum'], RequiredIntegerValidator::class, 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['product_id', 'order_id', 'price', 'q', 'sum'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'product_id'   => 'Товар',
            'product'      => 'Товар',
            'order_id'     => 'Заказ',
            'order'        => 'Заказ',
            'price'        => 'Цена',
            'q'            => 'Колич.',
            'sum'          => 'Сумма',
        ];
    }

    public function __toString()
    {
        return (string)$this->id;
    }
}