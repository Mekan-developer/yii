<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Region;
use kartik\daterange\DateRangePicker;
?>

<div class="report-search">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
    ]); ?>

    <!-- <?= $form->field($model, 'period')->input('date', ['placeholder' => 'Выберите период']) ?> -->
     <?=  $form->field($model, 'period')->widget(DateRangePicker::class, [
    'pluginOptions' => [
        'locale' => ['format' => 'DD.MM.YYYY'],
        'opens' => 'left',
    ],
]); ?> 

    
    <?= $form->field($model, 'region_id')->dropDownList(
        Region::find()->select(['name', 'id'])->indexBy('id')->column(),
        ['prompt' => 'Выберите район']
    ) ?>

    <div class="form-group">
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Сброс', ['report'], ['class' => 'btn btn-outline-secondary']) ?>
        <!-- <?= Html::resetButton('Сброс', ['class' => 'btn btn-outline-secondary']) ?> -->
    </div>

    <?php ActiveForm::end(); ?>
</div>
