<?php

namespace common\grid;

use yii\helpers\Html;

class CheckboxColumn extends \yii\grid\CheckboxColumn
{
    public $showId = false;
    public $format = 'raw';

    public $checkboxOptions = ['class' => 'CheckboxColumn'];

    public function init()
    {
        parent::init();
        \Yii::$app->view->registerAssetBundle(\common\assets\CheckboxColumnAsset::class);
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $value = parent::renderDataCellContent($model, $key, $index);

        if($this->showId) $value = Html::tag("span", $model->id) . $value;
        return $value;
    }
}
