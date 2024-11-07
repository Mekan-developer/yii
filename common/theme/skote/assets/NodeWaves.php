<?php

namespace common\theme\skote\assets;

use yii\web\AssetBundle;

class NodeWaves extends AssetBundle
{
    public $sourcePath = '@common/theme/skote/assets/node-waves';
    public $css = [
        'waves.min.css',
    ];
    public $js = [
        'waves.min.js',
    ];
    public $depends = [];
}
