<?php

namespace common\controllers;

use common\grid\GridModelActionsInterface;
use common\models\BaseActiveRecord;
use common\rbac\RbacAccessControl;
use common\widgets\Flashes;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\Inflector;

/**
 * BaseController implements the CRUD actions for selected model.
 */
abstract class BaseController extends RefController
{
    //public $layout = '';

    public $modelClass       = '';
    public $searchClass      = '';

    public $defaultAction    = 'index';

    public $indexTemplate  = 'index';
    public $viewTemplate   = 'view';
    public $createTemplate = 'edit';
    public $editTemplate   = 'edit';

    /**
     * @var integer
     * Number of records on page
     */
    public $recsOnPage = 20;


    public function behaviors()
    {
        return [
            'access' => [
                'class' => RbacAccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['$'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Lists all models.
     * @return mixed
     */
    public function actionIndex()
    {
        $params = Yii::$app->request->queryParams;

        if ($this->searchClass) {
            $searchModel = new $this->searchClass();
        } else {
            $searchModel = new $this->modelClass();
            $searchModel->scenario = BaseActiveRecord::SCENARIO_SEARCH;
        }

        $dataProvider = $searchModel->search($params, $this->recsOnPage);

        return $this->render($this->indexTemplate, [
            'title'        => $this->sysTitle(),
            'searchModel'  => $searchModel,
            'model'        => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if(!isset($model)) {
            Flashes::setError('Запись не найдена');
            return $this->actionIndex();
        }

        return $this->render($this->viewTemplate, [
            'title' => $this->sysTitle(),
            'model' => $model,
        ]);
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = $this->newModel();
        $model->scenario = BaseActiveRecord::SCENARIO_INSERT;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Flashes::setSuccess('Запись добавлена');
            return $this->redirect(['index']);
        } else {
            return $this->render($this->createTemplate, [
                'title' => $this->sysTitle(),
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionEdit($id)
    {
        $model = $this->findModel($id);
        if(!isset($model)) {
            Flashes::setError('Запись не найдена');
            return $this->redirect(['index']);
        }
        $model->scenario = BaseActiveRecord::SCENARIO_UPDATE;
        if ($model instanceof GridModelActionsInterface && !$model->actionAllowed('edit')) {
            return $this->redirect(['index']);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Flashes::setSuccess('Запись обновлена');
            return $this->redirect(['index']);
        }

        return $this->render($this->editTemplate, [
            'title' => $this->sysTitle(),
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->scenario = BaseActiveRecord::SCENARIO_DELETE;
        if(!isset($model)) {
            Flashes::setError('Запись не найдена');
            return $this->redirect(['index']);
        }
        if ($model instanceof GridModelActionsInterface && !$model->actionAllowed('delete')) {
            return $this->redirect(['index']);
        }

        if($model->delete())
        {
            Flashes::setSuccess('Запись удалена');
        }

        return $this->redirect2Referrer();
    }

    /**
     * Finds the model based on its primary key value.
     * @param integer $id
     * @return The loaded model
     */
    protected function findModel($id)
    {
        $modelClass = $this->modelClass;
        return $modelClass::findOne($id);
    }

    protected function newModel()
    {
        $modelClass = $this->modelClass;
        return new $modelClass();
    }

    protected function sysTitle()
    {
        return Inflector::camel2words($this->id);
    }

    protected function getTotalsForAllPages(ActiveDataProvider $dataProvider, $fields)
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }

        $select = [];
        foreach($fields as $fieldName) {
            $select[] = new Expression('SUM(`' . $fieldName . '`) as `' . $fieldName . '`');
        }
        /* @var ActiveQuery $query */
        $query = clone $dataProvider->query;
        $query->select($select)->asArray(true);
        $result = $query->one();

        return is_array($result) ? $result : [];
    }
}
