<?php

namespace common\grid;

use yii\grid\DataColumn;

class ListColumn extends DataColumn
{
    public $values = [];

    /**
     * @inheritdoc
     */
    public function getDataCellValue($model, $key, $index)
    {
        $value = $model->{$this->attribute};
        if(empty($value)) return '-';
        return $this->values[$value] ?? $value;
    }
}
