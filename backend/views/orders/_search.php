<?php

use yii\helpers\Html;
use common\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Order */
/* @var $form yii\widgets\ActiveForm */

$js = <<<JS
$(function() {
  $('#order-period').daterangepicker({
    timePicker: false,
    autoUpdateInput: false,
    locale: {
      format: 'DD.MM.YYYY',
      separator: ' - ',
      applyLabel: 'Применить',
      cancelLabel: 'Отмена',
      fromLabel: 'От',
      toLabel: 'До',
      customRangeLabel: 'Custom',
      weekLabel: 'Неделя',
      daysOfWeek: [
        'Вс',
        'Пн',
        'Вт',
        'Ср',
        'Чт',
        'Пт',
        'Сб'
       ],
      monthNames: [
        'Январь',
        'Февраль',
        'Март',
        'Апрель',
        'Май',
        'Июнь',
        'Июль',
        'Август',
        'Сентябрь',
        'Октябрь',
        'Ноябрь',
        'Декабрь'
      ],
      firstDay: 1
    }
  })
  .on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
  })
  .on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
  })
  .change(function() { this.form.submit(); });
});
JS;
\common\assets\DateRangeAsset::register($this);
$this->registerJs($js);
?>

<div class="form-search">
    <?php
    $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'period')->textInput(['id' => 'order-period', 'name' => 'period', 'class' => 'form-control']) ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'text')->textInput(['id' => 'order-text', 'name' => 'text', 'class' => 'form-control']) ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'region_id')->dropDownList([0 => '---'] + \common\models\Region::getList(), ['id' => 'order-region', 'name' => 'region', 'class' => 'form-select']) ?>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="" class="control-label">&nbsp;</label>
                <div class="input-group0">
                    <?= Html::submitButton('<i class="fa fa-search"></i>', ['class' => 'btn btn-primary', 'title' => 'Поиск']) ?>
                    <?= Html::submitButton('<i class="fa fa-trash"></i>', ['class' => 'btn btn-default btn-outline-dark', 'title' => 'Очистить', 'onclick' => "var form = jQuery(this.form); form.find('input[type=text]').val(''); form.find('select').val(0); return true;"]) ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
