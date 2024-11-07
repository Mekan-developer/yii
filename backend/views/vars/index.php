<?php

use yii\helpers\Html;
use common\grid\GridView;
use common\widgets\Card;

/* @var $this yii\web\View */
/* @var $searchModel common\models\Vars */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = ($title ?: Yii::$app->urlManager->getLastTitle());
?>

<?php Card::begin([]); ?>

<?php /* $this->render('_search', ['model' => $searchModel]); */ ?>

<?= GridView::widget([
    'actions' => \common\widgets\ActionButtons::widget(['defaultShowTitle' => false, 'defaultAccess' => '$', 'items' => [
        ['name' => 'create', 'options' => ['class' => 'btn btn-success btn-sm'], 'title' => 'Новая переменная', 'iconClass' => 'fa fa-plus', 'model' => $searchModel],
    ]]),
    'dataProvider' => $dataProvider,
    'columns' => [
        'key',
        ['attribute' => 'value', 'value' => function($model) {
            if ($model->type == \common\models\Vars::TYPE_DECIMAL) {
                $model->value = rtrim($model->value, '0');
            }
            return $model->value;
        }],
        //['header' => Lx::t('vars', 'var_description'), 'value' => function($model) { return Lx::t('vars', $model->key); }],
        'description',

        ['class' => 'common\grid\DateUserColumn', 'attribute' => 'modified', 'dateFormat' => 'dateTime', 'userAttribute' => 'modifier_id'],
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