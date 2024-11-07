<?php

namespace common\assets;

use yii\web\AssetBundle;

class ExtFormsInitAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/extform/';
    public $css = [
    ];
    public $js  = [
        'extform.js?v=2',
        'jquery.numeric.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
