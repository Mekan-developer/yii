<?php

namespace common\grid;

use yii\helpers\Html;

class UrlColumn extends \yii\grid\DataColumn
{
    public $emptyValue = '&nbsp;';
    public $format = 'raw';
    /**
     * @inheritdoc
     */
    public function getDataCellValue($model, $key, $index)
    {
        $url = parent::getDataCellValue($model, $key, $index);
        if(empty($url)) return $this->emptyValue;

        $info = parse_url($url);
        if(empty($info['scheme'])) {
            $url = 'http://' . $url;
        }

        $res = Html::a($url, $url, ['target' => '_blank']);
        return $res;
    }
}
