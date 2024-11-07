<?php

/** @var yii\web\View $this */
/** @var common\models\Order $model */

use common\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

?>
<?php $form = ActiveForm::begin(['options' => ['autocomplete' => 'off', 'enctype' => 'multipart/form-data']]); ?>

<div class="card mt-2">
    <div class="card-content">
        <div class="card-body">

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'date')->textInput(['class' => 'form-control', 'readonly' => true, 'disabled' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'region_id')->dropDownList([0 => '---'] + \common\models\Region::getList(), ['class' => 'form-select']) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'client')->textInput(['class' => 'form-control']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'phone')->textInput(['class' => 'form-control']) ?>
                </div>
            </div>

            <h5 class="fw-bold mt-4 mb-3"><?= $model->getAttributeLabel('editProducts') ?></h5>
            <div class="form-group mb-2">
                <?php echo \frontend\widgets\OrderProductsWidget::widget(['model' => $model, 'useExistintgQ' => false]) ?>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'sum')->textInput(['id' => 'sum', 'class' => 'form-control', 'readonly' => true, 'disabled' => true]) ?>
                </div>
            </div>

            <div>
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                <?php echo Html::a('Отмена', ['/'], ['class' => 'btn btn-soft-dark']) ?>
            </div>

        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<div class="modal modal-lg" id="products_modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавить товар</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input-group input-group">
                    <label class="input-group-text bg-body text-body" for="order-cancel_reason">Выберите товар</label>

                    <select id="order-product" class="form-select">
                        <?php $productsData = (array)\common\models\Product::getDataList() ?>
                        <option value="">Выберите...</option>
                        <?php foreach($productsData[0] as $prodId => $prodName) { ?>
                            <option value="<?= $prodId ?>" data-price="<?= $productsData[1][$prodId]['price'] ?? 0 ?>" data-q="<?= $productsData[1][$prodId]['q'] ?? 0 ?>"><?= $prodName ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning" id="btn-product-add" disabled>Добавить</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
