<?php

namespace common\assets;

use yii\web\AssetBundle;

class FuncyBoxAsset extends AssetBundle {

    /**
     * @inheritdoc
     */
    public $sourcePath = '@common/assets/fancybox/';

    public $js  = [
        'jquery.fancybox.js',
        'helpers/jquery.fancybox-buttons.js',
        'helpers/jquery.fancybox-media.js',
        'jquery.fancybox-setup.js',
    ];

    /**
     * @inheritdoc
     */
    public $css = [
        'jquery.fancybox.css',
        'helpers/jquery.fancybox-buttons.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
