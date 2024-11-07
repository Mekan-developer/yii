<?php

namespace backend\controllers;

use common\controllers\BaseController;
use common\grid\GridModelActionsInterface;
use common\models\BaseActiveRecord;
use common\models\User;
use common\widgets\Flashes;
use Yii;

/**
 * Users Controller
 */
class UsersController extends BaseController
{
    public $modelClass = User::class;

    public $indexTemplate  = 'index';
    public $viewTemplate   = 'view';
    public $createTemplate = 'edit';
    public $editTemplate   = 'edit';

//    /**
//     * Sign in as user.
//     * @return mixed
//     */
//    public function actionSignIn($id)
//    {
//        $userClass = \Yii::$app->user->identityClass;
//        $model     = $userClass::findOne($id);
//        if(!isset($model)) {
//            \app\widgets\Flashes::setError('Пользователь не найден.');
//            return $this->redirect2Referrer();
//        }
//
//        if (\Yii::$app->user->login($model, 0)) {
//            return $this->redirect(['/']);
//        }
//
//        return $this->redirect2Referrer();
//    }

    /**
     * Updates an existing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionPassword($id)
    {
        $model = $this->findModel($id);
        $model->scenario = BaseActiveRecord::SCENARIO_UPDATE;
        if(!isset($model)) {
            Flashes::setError('Запись не найдена');
            return $this->redirect(['index']);
        }
        if ($model instanceof GridModelActionsInterface && !$model->actionAllowed('password')) {
            return $this->redirect(['index']);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Flashes::setSuccess('Запись обновлена');
            return $this->redirect(['index']);
        }

        return $this->render('password', [
            'title' => $this->sysTitle(),
            'model' => $model,
        ]);
    }
}
