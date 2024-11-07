<?php

namespace common\grid;

class TranslationsColumn extends \yii\grid\DataColumn
{
    public $glue = ' ';
    public $glueLine = '<br/>';
    public $format = 'raw';

    /**
     * @inheritdoc
     */
    public function getDataCellValue($model, $key, $index)
    {
        $value = $model->__get($this->attribute);
        if (!is_array($value)) $value = (array)json_decode($value, true);
        if(!count($value)) return '-';

        $html = [];
        foreach($value as $lng => $val) {
            $html[] = '<span title="' . $lng . '" class="badge bg-info badge-lng ml-50 mt-25 mb-25">' . $lng . '</span>' . $this->glue . $val;
        }
        return implode($this->glueLine, $html);
    }
}
