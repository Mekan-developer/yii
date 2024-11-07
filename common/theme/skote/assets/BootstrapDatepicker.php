<?php

namespace common\theme\skote\assets;

use yii\web\AssetBundle;

class BootstrapDatepicker extends AssetBundle
{
    public $sourcePath = '@common/theme/skote/assets/bootstrap-datepicker';
    public $css = [
        'css/bootstrap-datepicker.min.css',
    ];
    public $js = [
        'js/bootstrap-datepicker.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
