<?php

namespace backend\assets;

use yii\web\View;
use yii\helpers\Url;
use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css?v=3',
    ];
    public $js = [
    ];
    public $depends = [
        \common\theme\skote\assets\SkoteAsset::class,
        \common\assets\ExtFormsInitAsset::class,
        \common\assets\BootstrapIconsAsset::class,
    ];

    /**
     * @inheritDoc
     */
    public static function register($view)
    {
        $js = 'var URL_ROOT = "' . Url::to(['/'], true) . '";' . "\r\n";
        $view->registerJs($js, View::POS_BEGIN);
        return parent::register($view);
    }
}
