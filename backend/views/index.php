<?php
die('test');
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Отчёт';
?>

<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_search', ['model' => $searchModel]); ?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ФИО клиента</th>
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <th>Товар <?= $i ?></th>
            <?php endfor; ?>
            <th>ИТОГО</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dataProvider->getModels() as $clientName => $products): ?>
            <tr>
                <td><?= Html::encode($clientName) ?></td>
                <?php 
                $total = 0;
                for ($i = 1; $i <= 12; $i++): 
                    $quantity = isset($products[$i]) ? $products[$i] : 0;
                    $total += $quantity;
                ?>
                    <td><?= Html::encode($quantity) ?></td>
                <?php endfor; ?>
                <td><?= Html::encode($total) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
