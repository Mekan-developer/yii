<?php

$db = require __DIR__ . '/db.php';

date_default_timezone_set('Europe/Moscow');
//date_default_timezone_set('UTC');

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'php:d.m.Y, D',
            'datetimeFormat' => 'php:d.m.Y, D H:i',
            'timeFormat' => 'php:H:i',
            'timeZone' => 'Europe/Moscow',
            'defaultTimeZone' => 'Europe/Moscow',
//            'timeZone' => 'UTC',
            'locale' => 'ru-RU',
        ],
        'upload' => [
            'class'           => 'common\components\MediaUpload',
            'storePath'       => '@files/store',
            'storeUrl'        => '@web/files/store',
            'cachePath'       => '@files/cache',
            'cacheUrl'        => '@web/files/cache',
            'hiddenPaths'     => false,
            'graphicsLibrary' => 'Imagick',
            'previewSize'     => 'x160',
            'softDelete'      => true,
        ],
        'db' => $db,
    ],
];
