<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class VarValuesWidgetAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/var-values';
    public $css = [
    ];
    public $js = [
        'var-values.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
    ];
}
