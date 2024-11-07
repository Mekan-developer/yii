<?php

namespace common\models;

use common\behaviors\OrderProductBehavior;
use common\behaviors\OrderProductSetupBehavior;
use common\validators\DbDateValidator;
use common\validators\RequiredIntegerValidator;
use common\widgets\Flashes;
use Yii;

/**
 * Order model
 *
 * @property integer $id
 * @property integer $number
 * @property string  $date
 * @property string  $client
 * @property string  $phone
 * @property integer $region_id
 * @property integer $positions
 * @property integer $q
 * @property integer $sum
 * @property integer $is_deleted
 * @property string  $created
 * @property integer $creator_id
 * @property string  $modified
 * @property integer $modifier_id
 *
 * @property Product[] $products
 */
class Order extends BaseActiveRecord
{
    public $text = '';
    public $period = '';
    public $periodStart = '';
    public $periodEnd = '';

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(OrderProduct::class, ['order_id' => 'id'])->andWhere('`is_deleted` = 0');
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = [
            'class' => OrderProductBehavior::class,
            'relations' => [
                'editProducts' => [
                    OrderProduct::class, 'order_id', ['product_id', 'q'],
                ],
            ],
        ];
        $behaviors['setup'] = [
            'class' => OrderProductSetupBehavior::class,
            'relations' => [
                'setupProducts' => [
                    OrderProduct::class, 'order_id', ['product_id', 'q'],
                ],
            ],
        ];
        return $behaviors;
    }

    /**
     * @inheritDoc
     */
    public function search($params, $recsOnPage = 20)
    {
        $this->region_id = (int)trim($params['region'] ?? 0);
        $this->text = trim($params['text'] ?? '');
        $this->period = trim($params['period'] ?? '');

        $dataProvider = parent::search($params, $recsOnPage);
        if ($dataProvider) {
            $p = [];
            if (preg_match('/([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})[^0-9]+([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})/', $this->period, $p)) {
                $this->periodStart = $p[3] . '-' . $p[2] . '-' . $p[1];
                $this->periodEnd = $p[6] . '-' . $p[5] . '-' . $p[4];
            }

            $query = $dataProvider->query;
            if ($this->periodStart) {
                $query->andWhere(['>=', 'date', $this->periodStart]);
            }
            if ($this->periodEnd) {
                $query->andWhere(['<=', 'date', $this->periodEnd]);
            }
            if ($this->region_id) {
                $query->andWhere(['region_id' => $this->region_id]);
            }

            if ($this->text) {
                $query->andWhere(['OR',
                    ['like', 'client', $this->text],
                    ['like', 'phone', $this->text],
                ]);
            }
        }

        return $dataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date'], DbDateValidator::class, 'timeZone' => 'Europe/Moscow', 'format' => 'php:d.m.Y', 'timestampAttributeFormat' => 'php:Y-m-d', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['client', 'phone', 'date'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['client', 'phone'], 'string', 'max' => 250],
            [['region_id', 'number', 'sum'], RequiredIntegerValidator::class, 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['region_id', 'number', 'sum'], 'integer'],

            [['editProducts', 'setupProducts'], 'safe', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'period'       => 'Период',
            'number'       => 'Номер',
            'date'         => 'Дата заказа',
            'client'       => 'ФИО клиента',
            'phone'        => 'Номер телефона',
            'region_id'    => 'Район',
            'region'       => 'Район',
            'positions'    => 'Позиций',
            'q'            => 'Колич. товаров',
            'sum'          => 'Сумма',
            'text'         => 'Текст',
            'editProducts' => 'Товары',
        ];
    }

    public function __toString()
    {
        return (string)$this->number;
    }

    public function prepareNewModel()
    {
        $max = (int)Order::findActive()->select('number')->orderBy(['number' => SORT_DESC])->limit(1)->scalar();
        $this->number = $max + 1;
        $this->date = date('Y-m-d');
    }

    public function transactions()
    {
        return [
            static::SCENARIO_INSERT => static::OP_ALL,
            static::SCENARIO_UPDATE => static::OP_ALL,
        ];
    }

    public function beforeValidate()
    {
        $this->sum = 1;
        return parent::beforeValidate();
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $sum = $positions = $q = 0;
        foreach ($this->products as $product) {
            $positions++;
            $sum += $product->sum;
            $q += $product->q;
        }
        Yii::$app->db->createCommand()->update(static::tableName(), ['q' => $q, 'positions' => $positions, 'sum' => $sum], 'id = :id', [':id' => $this->id])->execute();
    }

    protected function insertInternal($attributes = null)
    {
        $result = parent::insertInternal($attributes);

        if (!$this->behaviors['setup']->isCorrectlySaved()) {
            Flashes::setError('Количество товара на складе изменилось! Недосточное товара для покупки.');
            return false;
        }

        return $result;
    }

    public function delete()
    {
        $tr = Yii::$app->db->beginTransaction();
        $res = parent::delete();
        if ($res) {
            foreach($this->products as $product) {
                $prod = Product::findOne($product->product_id);
                if (!$prod) continue;

                $prod->q += $product->q;
                $prod->save(false);
            }
            $tr->commit();
        } else {
            $tr->rollBack();
        }

        return $res;
    }
}