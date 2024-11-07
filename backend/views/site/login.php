<?php
/* @var $this \yii\web\View */


use common\widgets\ActiveForm;

?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card overflow-hidden">
            <div class="bg-primary bg-soft text-center">
                <h4 class="text-black p-4 m-0"><?= Yii::$app->name ?></h4>
            </div>
            <div class="card-body pt-0">
                <div class="p-2">
                    <div class="mt-4 text-center">
                        <h5 class="font-size-14 mb-3"><?= 'Войдите, чтобы начать сеанс' ?></h5>
                    </div>
                    <?php $form = ActiveForm::begin(['id' => 'login-form']) ?>

                    <?= $form->field($model,'email', [
                        'options' => ['class' => 'form-group mb-3'],
                    ])
                        ->extTextInput(['placeholder' => 'Введите адрес эл. почты'], ['append' => '<span class="input-group-text"><i class="mdi mdi-email-outline"></i></span>']) ?>

                    <?= $form->field($model,'password', [
                        'options' => ['class' => 'form-group mb-3'],
                    ])
                        ->extPasswordInput(['placeholder' => 'Введите пароль'], ['append' => '<span class="input-group-text"><i class="mdi mdi-lock-outline"></i></span>']) ?>

                        <div class="mt-3 d-grid">
                            <button class="btn btn-primary waves-effect waves-light" type="submit"><?= 'Войти' ?></button>
                        </div>

                    <?php ActiveForm::end(); ?>
                </div>

            </div>
        </div>

    </div>
</div>
