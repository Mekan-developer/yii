<?php

use yii\helpers\Html;
use common\widgets\ActiveForm;
use common\widgets\Card;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $title;
$this->params['breadcrumbs'][] = $title;

?>

<?php Card::begin([]); ?>

<div class="edit-form">

    <?php $form = ActiveForm::begin(['options' => ['autocomplete' => 'off', 'enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'firstname')->textInput(['maxlength' => true, 'readonly' => true, 'disabled' => true]) ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'lastname')->textInput(['maxlength' => true, 'readonly' => true, 'disabled' => true]) ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'surname')->textInput(['maxlength' => true, 'readonly' => true, 'disabled' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'readonly' => true, 'disabled' => true]) ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'readonly' => true, 'disabled' => true]) ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'type')->dropDownList([0 => '---'] + \common\models\User::listTypes(), ['readonly' => true, 'disabled' => true]) ?>
        </div>
    </div>

    <hr />

    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <?= $form->field($model, 'password_new')->passwordInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true]) ?>
        </div>
    </div>


    <div>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php Card::end() ?>
