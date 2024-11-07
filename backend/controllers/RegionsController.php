<?php

namespace backend\controllers;

use common\controllers\BaseController;
use common\models\Region;

/**
 * Regions Controller
 */
class RegionsController extends BaseController
{
    public $modelClass = Region::class;

    public $indexTemplate  = 'index';
    public $viewTemplate   = 'view';
    public $createTemplate = 'edit';
    public $editTemplate   = 'edit';
}
