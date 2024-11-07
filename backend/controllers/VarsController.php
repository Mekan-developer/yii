<?php

namespace backend\controllers;

use common\controllers\BaseController;
use common\models\Vars;

class VarsController extends BaseController
{
    public $modelClass = Vars::class;

    /**
     * Displays a single model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
    }
}
