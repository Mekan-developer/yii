<?php

namespace common\assets;

use yii\web\AssetBundle;

class PlaceholderAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/placeholders/';
    public $css = [
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
