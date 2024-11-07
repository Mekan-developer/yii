<?php
use yii\helpers\Html;

/** @var array $reportData */
/** @var array $totals */
/** @var string $startDate */
/** @var string $endDate */
/** @var int $regionId */

?>

<h1>Client Product Report</h1>

<!-- Render search form -->
<?= $this->render('_search', [
    'model' => $searchModel,
]) ?>

<!-- Display filter information if applied -->
<div>
    <p>Period: <?= Html::encode($startDate) ?> to <?= Html::encode($endDate) ?></p>
    <p>Region: <?= Html::encode($regionId) ?></p>
</div>

<!-- Report Table -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Client</th>
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <th>Product <?= $i ?></th>
            <?php endfor; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($reportData as $data): ?>
            <tr>
                <td><?= Html::encode($data['client']) ?></td>
                <?php foreach ($data['products'] as $quantity): ?>
                    <td><?= Html::encode($quantity) ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td><strong>Total</strong></td>
            <?php foreach ($totals as $total): ?>
                <td><strong><?= Html::encode($total) ?></strong></td>
            <?php endforeach; ?>
        </tr>
    </tfoot>
</table>
