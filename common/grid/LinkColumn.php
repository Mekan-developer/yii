<?php

namespace common\grid;

use yii\grid\DataColumn;

class LinkColumn extends DataColumn
{
    protected $keyValue = 0;
    protected static $cache = [];
    public $targetClass = '';

    /**
     * @inheritdoc
     */
    public function getDataCellValue($model, $key, $index)
    {
        if(empty($this->targetClass)) return '-';
        $targetClass = $this->targetClass;

        $this->keyValue = $model->{$this->attribute};
        if(empty($this->keyValue)) return '-';

        if(!isset(self::$cache[$this->attribute][$this->keyValue]))
        {
            $model = $targetClass::findOne($this->keyValue);
            if(!$model) return '-';
            self::$cache[$this->attribute][$this->keyValue] = (string)$model;
        }
        return self::$cache[$this->attribute][$this->keyValue];
    }
}
