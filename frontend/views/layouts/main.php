<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;

\common\theme\skote\assets\SkoteAsset::register($this);
\common\assets\ExtFormsInitAsset::register($this);
\common\assets\BootstrapIconsAsset::register($this);
$isLogged = !Yii::$app->user->isGuest;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php $this->registerCsrfMetaTags() ?>
    <base href="<?= Url::to(['/'], true) ?>"/>
    <title><?= Html::encode($this->title) ?></title>

    <link rel="apple-touch-icon" sizes="180x180" href="favicon3/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon3/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon3/favicon-16x16.png">
    <link rel="manifest" href="favicon3/site.webmanifest">
    <link rel="mask-icon" href="favicon3/safari-pinned-tab.svg" color="#21748b">
    <link rel="shortcut icon" href="favicon3/favicon.ico">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-config" content="favicon3/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    <?php /*<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css" rel="stylesheet"> */ ?>
    <?php $this->head() ?>
</head>

<body data-topbar="dark" data-layout="horizontal">
<?php $this->beginBody() ?>

<div id="layout-wrapper">

    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">
                <div class="navbar-brand-box">
                    <a href="<?= Url::to(['/']) ?>" class="logo font-size-16 text-white">
                        <?= Yii::$app->name ?>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="main-content">

        <div class="page-content admin100">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0 font-size-18"><?= $this->title ?></h4>

                            <div class="page-title-right">
                                <?= \common\theme\skote\widgets\Breadcrumbs::widget(['homeLink' => ['url' => '/', 'label' => '<i class="fa fa-home"></i>']]) ?>
                            </div>

                        </div>
                    </div>
                </div>

                <?= \common\widgets\Flashes::widget() ?>

                <?= $content ?>

            </div>
        </div>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <?= date('Y') ?> © <?= Yii::$app->name ?>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end d-none d-sm-block">
                            ...
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
