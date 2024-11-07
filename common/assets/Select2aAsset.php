<?php

namespace common\assets;

use yii\web\AssetBundle;

class Select2aAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/select2a/';

    public $css = [
        'css/select2.min.css',
    ];

    public $js = [
        'js/select2.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
