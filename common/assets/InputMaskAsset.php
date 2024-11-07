<?php

namespace common\assets;

use yii\jui\JuiAsset;
use yii\web\AssetBundle;

class InputMaskAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/input-mask/';

    public $css = [
    ];

    public $js  = [
        'jquery.inputmask.min.js',
        'bindings/inputmask.binding.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
