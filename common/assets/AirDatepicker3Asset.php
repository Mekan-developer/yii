<?php

namespace common\assets;

use yii\web\AssetBundle;

class AirDatepicker3Asset extends AssetBundle
{
    public $sourcePath = '@common/assets/air-datepicker-3/';
    public $css = [
        'air-datepicker.css',
    ];
    public $js  = [
        'air-datepicker.js',
    ];
    public $depends = [
    ];
}
