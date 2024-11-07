<?php

use yii\helpers\Html;
use common\grid\GridView;
use common\widgets\Card;

/* @var $this yii\web\View */
/* @var $searchModel common\models\Order */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->urlManager->getLastTitle();
?>

<?php Card::begin([]); ?>

<?php echo $this->render('_search', ['model' => $searchModel]);  ?>

<?= GridView::widget([
    'actions' => \common\widgets\ActionButtons::widget(['defaultShowTitle' => false, 'defaultAccess' => '$', 'items' => [
        ['name' => 'create', 'options' => ['class' => 'btn btn-success btn-sm'], 'title' => 'Новый товар', 'iconClass' => 'fa fa-plus', 'model' => $searchModel],
        ['name' => 'export', 'options' => ['class' => 'btn btn-warning btn-sm'], 'title' => 'Экспорт в Excel', 'showTitle' => true, 'iconClass' => 'fa fa-file-excel me-2', 'model' => $searchModel],
    ]]),
    'dataProvider' => $dataProvider,
    'columns' => [
        'number',
//        ['attribute' => 'id'],
        'date',
        'client',
        'phone',
        ['attribute' => 'region_id', 'class' => \common\grid\LinkColumn::class, 'targetClass' => \common\models\Region::class],
        'sum',
        [
            'class' => 'common\grid\ActionColumn',
            'defaultShowTitle' => false,
            'buttons' => [
                'edit'   => ['icon' => 'fa fa-edit', 'title' => 'Редактировать'],
                'sep1'   => [],
                'delete' => ['icon' => 'fa fa-trash', 'class' => 'btn btn-danger btn-sm', 'title' =>'Удаление', 'confirm' => 'Удалить ?', 'isPost' => true],
            ],
        ],
    ],
]); ?>

<?php Card::end(); ?>