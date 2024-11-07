<?php

use yii\helpers\Html;
use common\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Region */

$this->title = Yii::$app->urlManager->getLastTitle();

?>

<?php /*<div class="">

    <a href="<?= \yii\helpers\Url::to(['/drivers/view', 'id' => $model->id]) ?>" class="btn btn-rounded btn-primary mb-2 me-2">Информация</a>
    <a href="<?= \yii\helpers\Url::to(['/drivers/trips', 'id' => $model->id]) ?>" class="btn btn-rounded btn-outline-secondary mb-2 me-2">Поездки</a>

</div>*/ ?>

<?php $form = ActiveForm::begin(['options' => ['autocomplete' => 'off', 'enctype' => 'multipart/form-data']]); ?>

<div class="card mt-2">
    <div class="card-content">
        <div class="card-body">

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'name')->textInput(['class' => 'form-control']) ?>
                </div>
            </div>

            <div>
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                <?php echo Html::a('Отмена', $this->context->getReferrer(), ['class' => 'btn btn-soft-dark']) ?>
            </div>

        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
