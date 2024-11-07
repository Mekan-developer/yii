<?php

namespace common\assets;

use yii\web\AssetBundle;

class PoppinsFontAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/poppins-font';
    public $css = [
        'css.css?v=1',
    ];
    public $js = [];
}
