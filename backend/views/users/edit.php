<?php

use yii\helpers\Html;
use common\widgets\ActiveForm;
use common\widgets\Card;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$action = ($model->isNewRecord ? 'Новый пользователь' : 'Редактирование');
$this->title = Html::encode(Yii::$app->urlManager->getLastTitle() . ' - ' . $action .
    ($model->isNewRecord ? '' : ' «' . (string)$model . '»' ));

if (!$model->isNewRecord) $this->params['breadcrumbs'][] = (string)$model;
$this->params['breadcrumbs'][] = $action;
?>

<?php Card::begin([]); ?>

<div class="edit-form">

    <?php $form = ActiveForm::begin(['options' => ['autocomplete' => 'off', 'enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?php
            $options = $model->id == 1 ? ['disabled' => true, 'readonly' => true, 'id' => 'user-type'] : ['id' => 'user-type'];
            ?>
            <?= $form->field($model, 'type')->dropDownList([0 => '---'] + \common\models\User::listTypes(), $options) ?>
        </div>
    </div>

    <?php if ($model->isNewRecord) { ?>
    <hr />

    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'password_new')->passwordInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
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
