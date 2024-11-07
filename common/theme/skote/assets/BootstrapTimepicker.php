<?php

namespace common\theme\skote\assets;

use yii\web\AssetBundle;

class BootstrapTimepicker extends AssetBundle
{
    public $sourcePath = '@common/theme/skote/assets/bootstrap-timepicker';
    public $css = [
        'css/bootstrap-timepicker.min.css',
    ];
    public $js = [
        'js/bootstrap-timepicker.min.js',
    ];
    public $depends = [];
}
