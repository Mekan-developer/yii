<?php

namespace common\models;

use common\grid\GridModelActionsInterface;
use common\widgets\Flashes;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * Base ActiveRecord for frontend
 */
class BaseActiveRecord extends \yii\db\ActiveRecord implements GridModelActionsInterface
{
    const SCENARIO_INSERT = 'insert';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_DELETE = 'delete';
    const SCENARIO_SEARCH = 'search';

    /**
     * Use "SOFT" deletion
     * @var boolean
     */
    protected static $safeDelete = true;

    /**
     * Show error messages
     * @var boolean
     */
    protected $showErrors = true;

    /**
     * Performs the data validation.
     *
     * @inheritDoc
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        $res = parent::validate($attributeNames, $clearErrors);
        if($this->showErrors && !Yii::$app->request->isConsoleRequest && !$res && is_array($this->errors) && count($this->errors) > 0) {
            foreach($this->errors as $field => $messages) {
                foreach($messages as $msg) {
                    Flashes::setError($msg);
                }
            }
        }
        return $res;
    }

    protected $defaultOrder = ['id' => SORT_DESC];
    protected $sortingEnabled = true;

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $recsOnPage = 20)
    {
        $this->load($params);

        // Prepare data provider
        $query = static::findActive();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => $this->sortingEnabled ? ['defaultOrder' => $this->defaultOrder] : false,
            'pagination' => $recsOnPage > 0 ? [
                'defaultPageSize' => $recsOnPage,
            ] : false,
        ]);

        // Return data provider
        return $dataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Date
            $date = date('Y-m-d H:i:s');
            if ($this->hasAttribute('modified')) {
                $this->modified = $date;
            }
            if ($this->isNewRecord && $this->hasAttribute('created')) {
                $this->created = $date;
            }

            if (\Yii::$app->request->isConsoleRequest) {
                return true;
            }

            // User
            if (!\Yii::$app->user->isGuest) {
                $userId = \Yii::$app->user->identity->getId();
                if ($this->hasAttribute('modifier_id')) {
                    $this->modifier_id = $userId;
                }
                if ($this->isNewRecord && $this->hasAttribute('creator_id')) {
                    $this->creator_id = $userId;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        if (static::$safeDelete) {
            $this->is_deleted = 1;
            $result = $this->save(false);
        } else {
            $result = parent::delete();
        }
        return $result;
    }

    public function isDeleted()
    {
        if (!static::$safeDelete) return false;
        return $this->is_deleted > 0;
    }

    /**
     * @return ActiveQuery
     */
    public static function findActive()
    {
        /* @var ActiveQuery $query */
        $query = static::find();
        if (static::$safeDelete) $query->andWhere(['is_deleted' => 0]);
        return $query;
    }

    public static function findActiveOne($condition)
    {
        /* @var ActiveQuery $query */
        $query = static::findByCondition($condition);
        if (static::$safeDelete) $query->andWhere(['is_deleted' => 0]);
        return $query->one();
    }

    /**
     * Converts object to string
     * @return string
     */
    public function __toString()
    {
        return get_class($this) . ":" . $this->id;
    }

    /**
     * Return is allowed selected action for current model or not
     * @return boolean
     */
    public function actionAllowed($name)
    {
        return true;
    }

    public static function sortByName($a, $b) {
        $aName = mb_strtolower($a);
        $bName = mb_strtolower($b);
        if ($aName > $bName) {
            return 1;
        } elseif ($aName < $bName) {
            return -1;
        }
        return 0;
    }

    /**
     * Returns list of active objects
     * @param string $keyField
     * @param string $valueField
     * @param boolean $sortByValue
     * @return array
     */
    public static function listAll($keyField = 'id', $valueField = null, $sortByValue = true)
    {
        if($valueField == NULL) {
            $models = static::findActive()->all();
            $tmp = [];
            foreach($models as $model) {
                $tmp[$model->$keyField] = (string)$model;
            }
            if ($sortByValue) {
                asort($tmp);
//                usort($tmp, '\common\models\BaseActiveRecord::sortByName');
            }
            return $tmp;
        }

        $query = static::findActive()
            ->select([$keyField, $valueField])
            ->asArray();
        if ($sortByValue) {
            $query->orderBy([$valueField => SORT_ASC]);
        }

        return ArrayHelper::map($query->all(), $keyField, $valueField);
    }

    /**
     * Returns a list of scenarios and the corresponding active attributes.
     *
     * An active attribute is one that is subject to validation in the current scenario.
     * The returned array should be in the following format:
     *
     * ```php
     * [
     *     'scenario1' => ['attribute11', 'attribute12', ...],
     *     'scenario2' => ['attribute21', 'attribute22', ...],
     *     ...
     * ]
     * ```
     *
     * By default, an active attribute is considered safe and can be massively assigned.
     * If an attribute should NOT be massively assigned (thus considered unsafe),
     * please prefix the attribute with an exclamation character (e.g. `'!rank'`).
     *
     * The default implementation of this method will return all scenarios found in the [[rules()]]
     * declaration. A special scenario named [[SCENARIO_DEFAULT]] will contain all attributes
     * found in the [[rules()]]. Each scenario will be associated with the attributes that
     * are being validated by the validation rules that apply to the scenario.
     *
     * @return array a list of scenarios and the corresponding active attributes.
     */
    public function scenarios()
    {
        $scenarios = [
            static::SCENARIO_DEFAULT => [],
            static::SCENARIO_INSERT => [],
            static::SCENARIO_UPDATE => [],
            static::SCENARIO_DELETE => [],
            static::SCENARIO_SEARCH => [],
        ];
        foreach ($this->getValidators() as $validator) {
            foreach ($validator->on as $scenario) {
                $scenarios[$scenario] = [];
            }
            foreach ($validator->except as $scenario) {
                $scenarios[$scenario] = [];
            }
        }
        $names = array_keys($scenarios);

        foreach ($this->getValidators() as $validator) {
            if (empty($validator->on) && empty($validator->except)) {
                foreach ($names as $name) {
                    foreach ($validator->attributes as $attribute) {
                        $scenarios[$name][$attribute] = true;
                    }
                }
            } elseif (empty($validator->on)) {
                foreach ($names as $name) {
                    if (!in_array($name, $validator->except, true)) {
                        foreach ($validator->attributes as $attribute) {
                            $scenarios[$name][$attribute] = true;
                        }
                    }
                }
            } else {
                foreach ($validator->on as $name) {
                    foreach ($validator->attributes as $attribute) {
                        $scenarios[$name][$attribute] = true;
                    }
                }
            }
        }

        foreach ($scenarios as $scenario => $attributes) {
            if (!empty($attributes)) {
                $scenarios[$scenario] = array_keys($attributes);
            }
        }

        return $scenarios;
    }
}
