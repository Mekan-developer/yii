<?php

use common\models\Region;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Regions;

// Fetch regions for dropdown
$regions = Region::find()->select(['name', 'id'])->indexBy('id')->column();

?>

<div class="report-search">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['report-dev/index'],
    ]); ?>

    <div class="form-group">
        <?= $form->field($model, 'start_date')->input('date', [
            'value' => Yii::$app->request->get('start_date'),
        ])->label('Start Date') ?>

        <?= $form->field($model, 'end_date')->input('date', [
            'value' => Yii::$app->request->get('end_date'),
        ])->label('End Date') ?>

        <?= $form->field($model, 'region_id')->dropDownList($regions, [
            'prompt' => 'Select Region',
            'value' => Yii::$app->request->get('region_id'),
        ])->label('Region') ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Filter', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
