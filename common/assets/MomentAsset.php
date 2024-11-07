<?php

namespace common\assets;

use yii\web\AssetBundle;

class MomentAsset extends AssetBundle {

    /**
     * @inheritdoc
     */
    public $sourcePath = '@common/assets/moment/';

    public $js  = [
        'moment.min.js',
    ];

    /**
     * @inheritdoc
     */
    public $css = [];

    public $depends = [
    ];
}
