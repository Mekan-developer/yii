<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Regions;
?>

<div class="report-search">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'period')->input('date', ['placeholder' => 'Выберите период']) ?>
    
    <?= $form->field($model, 'region_id')->dropDownList(
        Regions::find()->select(['name', 'id'])->indexBy('id')->column(),
        ['prompt' => 'Выберите район']
    ) ?>

    <div class="form-group">
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Сброс', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
