<?php

namespace common\controllers;


use Yii;
use yii\web\Controller;

abstract class RefController extends Controller
{
    protected $_referrer = '';

    public function init()
    {
        $srvRef = Yii::$app->request->getReferrer();
        if ($srvRef) {
            $this->_referrer = $srvRef;
        }
        $getRef = Yii::$app->request->get('_referrer', '');
        if ($getRef) {
            $this->_referrer = $getRef;
        }
        $postRef = Yii::$app->request->post('_referrer', '');
        if ($postRef) {
            $this->_referrer = $postRef;
        }
        parent::init();
    }

    public function getReferrer()
    {
        return $this->_referrer;
    }

    public function redirect2Referrer($url = '', $notSelfRedirect = true)
    {
        if (!empty($this->_referrer) && (!$notSelfRedirect || strpos($this->_referrer, $_SERVER['REQUEST_URI']) === false)) {
            return $this->redirect($this->_referrer);
        }
        if (!empty($url)) {
            return $this->redirect($url);
        }
        return $this->redirect(['index']);
    }
}
