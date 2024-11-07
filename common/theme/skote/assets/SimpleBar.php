<?php

namespace common\theme\skote\assets;

use yii\web\AssetBundle;

class SimpleBar extends AssetBundle
{
    public $sourcePath = '@common/theme/skote/assets/simplebar';
    public $css = [
        'simplebar.min.css',
    ];
    public $js = [
        'simplebar.min.js',
    ];
    public $depends = [];
}
