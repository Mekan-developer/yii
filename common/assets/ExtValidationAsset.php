<?php

namespace common\assets;

use yii\web\AssetBundle;

class ExtValidationAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/extform/';
    public $css = [
    ];
    public $js  = [
        'validation.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
