<?php

use yii\helpers\Html;
use common\grid\GridView;
use common\widgets\Card;

/* @var $this yii\web\View */
/* @var $searchModel common\models\User */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->urlManager->getLastTitle();
?>

<?php Card::begin([]); ?>

<?= $this->render('_search', ['model' => $searchModel]); ?>

<?= GridView::widget([
    'actions' => \common\widgets\ActionButtons::widget(['defaultShowTitle' => false, 'defaultAccess' => '$', 'items' => [
        ['name' => 'create', 'options' => ['class' => 'btn btn-success btn-sm'], 'title' => 'Новый пользователь', 'iconClass' => 'fa fa-plus', 'model' => $searchModel],
    ]]),
    'dataProvider' => $dataProvider,
    'columns' => [
        ['attribute' => 'id'],
        'lastname',
        'firstname',
        'surname',
        'email',
        'phone',
        ['attribute' => 'type', 'format' => 'raw', 'value' => function($model) {
            return $model->typeName;
        } ],
        [
            'class' => 'common\grid\ActionColumn',
            'defaultShowTitle' => false,
            'buttons' => [
                'edit'   => ['icon' => 'fa fa-edit', 'title' => 'Редактировать'],
                'password' => ['icon' => 'fa fa-key', 'title' => 'Поменять пароль'],
                'sep1'   => [],
                'delete' => ['icon' => 'fa fa-trash', 'class' => 'btn btn-danger btn-sm', 'title' =>'Удаление', 'confirm' => 'Удалить ?', 'isPost' => true],
            ],
        ],
    ],
]); ?>

<?php Card::end(); ?>