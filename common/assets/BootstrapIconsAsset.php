<?php

namespace common\assets;

use yii\web\AssetBundle;

class BootstrapIconsAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/bootstrap-icons';
    public $css = [
        'bootstrap-icons.min.css?v=4',
    ];
    public $js = [];
}
