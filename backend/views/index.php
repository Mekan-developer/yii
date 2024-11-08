<?php
use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Product;
use common\models\OrderProduct;

$this->title = 'Отчёт';
?>

<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_search', ['model' => $searchModel]); ?>

<?php
// Получаем список товаров из модели Product
$products = Product::find()->select(['id', 'name'])->orderBy('id')->all();
?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ФИО клиента</th>
            <?php foreach ($products as $product): ?>
                <th><?= Html::encode($product->name) ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php 
        // Переменные для хранения общего итога по каждому товару
        $totalByProduct = array_fill(0, count($products), 0);
        $grandTotal = 0;

        // Перебираем все заказы из dataProvider
        foreach ($dataProvider->getModels() as $order): ?>
            <tr>
                <td><?= Html::encode($order->client) ?></td> <!-- Используйте $order->client, если это объект -->
                <?php 
                $total = 0;
        
                foreach ($products as $index => $product): 
                    $orderProduct = OrderProduct::find()
                        ->where(['order_id' => $order->id, 'product_id' => $product->id])
                        ->one();
        
                    $quantity = $orderProduct ? $orderProduct->q : 0;
                    $total += $quantity;
                    $totalByProduct[$index] += $quantity;
                ?>
                    <td><?= Html::encode($quantity) ?></td>
                <?php endforeach; ?>
            </tr>
        <?php 
        $grandTotal += $total;
        endforeach; 
        
        ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #f8d7da;">
            <td><strong>ИТОГО</strong></td>
            <?php foreach ($totalByProduct as $total): ?>
                <td><strong><?= Html::encode($total) ?></strong></td>
            <?php endforeach; ?>
        </tr>
    </tfoot>
</table>
<?= Html::beginForm(['report-dev/export-excel'], 'post'); ?>
<?= Html::hiddenInput('ReportSearch[period]', $searchModel->period); ?>
<?= Html::hiddenInput('ReportSearch[region_id]', $searchModel->region_id); ?>
<?= Html::submitButton('Экспорт в Excel', ['class' => 'btn btn-success']); ?>
<?= Html::endForm(); ?>
