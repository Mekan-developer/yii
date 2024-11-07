<?php

namespace backend\controllers;

use common\controllers\BaseController;
use common\models\Product;

/**
 * Products Controller
 */
class ProductsController extends BaseController
{
    public $modelClass = Product::class;

    public $indexTemplate  = 'index';
    public $viewTemplate   = 'view';
    public $createTemplate = 'edit';
    public $editTemplate   = 'edit';
}
