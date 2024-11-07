<?php

namespace backend\controllers;

use common\controllers\RefController;
use common\models\BaseActiveRecord;
use common\models\Client;
use common\models\Contract;
use common\models\Driver;
use common\models\LoginForm;
use common\models\RouteLength;
use common\widgets\Flashes;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;

class SiteController extends RefController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'ways'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'profile', 'cli-chld'/*, 'cli-drv', 'drv-cli'*/],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['$'],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
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
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->redirect(['/orders']);
//        return $this->render('index');
    }

    /**
     * Displays profile page.
     *
     * @return string
     */
    public function actionProfile()
    {
        $profile = 'Профиль';
        if (Yii::$app->urlManager instanceof \common\web\UrlManager) {
            Yii::$app->urlManager->clear();
            Yii::$app->urlManager->addBreadcrumb($profile);
            Yii::$app->urlManager->addTitle($profile);
        }

        $model = Yii::$app->user->identity;
        $model->scenario = BaseActiveRecord::SCENARIO_UPDATE;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Flashes::setSuccess('Запись обновлена.');
            return $this->redirect2Referrer('', false);
        }

        return $this->render('profile', [
            'title' => $profile,
            'model' => $model,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $this->layout = 'main-login';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
