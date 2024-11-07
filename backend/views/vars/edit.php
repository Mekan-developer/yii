<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\widgets\Card;
use common\models\Vars;

/* @var $this yii\web\View */
/* @var $model common\models\Vars */
/* @var string $title */

$action = ($model->isNewRecord ? 'Новая переменная' : 'Редактирование');
$this->title = Html::encode(($title ?: Yii::$app->urlManager->getLastTitle()) . ' - ' . $action .
    ($model->isNewRecord ? '' : ' «' . Html::encode((string)$model) . '»' ));

if (!$model->isNewRecord) $this->params['breadcrumbs'][] = (string)$model;
$this->params['breadcrumbs'][] = $action;

$editOptions = $model->isNewRecord ? [] : ['readonly' => true, 'disabled' => true];
?>

<?php Card::begin([]); ?>

<div class="edit-form">

    <?php $form = ActiveForm::begin(['options' => ['autocomplete' => 'off', 'enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <?= $form->field($model, 'key')->textInput(['maxlength' => true, 'class' => 'form-control'] + $editOptions) ?>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <?= $form->field($model, 'type')->dropDownList([0 => '---'] + Vars::getVarTypes(), $editOptions) ?>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?php if (!$model->isNewRecord && $model->type == \app\models\Vars::TYPE_LIST) { ?>
        <div class="row">
            <div class="col-md-6">
                <?php echo \base\widgets\VarValuesWidget::widget(['model' => $model]) ?>
            </div>
        </div>
    <?php } ?>

    <div>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Отмена', $this->context->getReferrer(), ['class' => 'btn btn-soft-dark']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php Card::end() ?>
