<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;

\backend\assets\AppAsset::register($this);
$isLogged = !Yii::$app->user->isGuest;
\common\assets\AddToHomeScreenAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <?php $this->registerCsrfMetaTags() ?>
    <base href="<?= Url::to(['/'], true) ?>"/>
    <title><?= Html::encode($this->title) ?></title>

    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="manifest" href="favicon/site.webmanifest">
    <link rel="mask-icon" href="favicon/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="favicon/favicon.ico">
    <meta name="apple-mobile-web-app-title" content="Автоняня">
    <meta name="application-name" content="Автоняня">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="favicon/mstile-144x144.png">
    <meta name="msapplication-config" content="favicon/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    <?php $this->head() ?>
</head>

<body data-topbar="dark" class="h-100">
<?php $this->beginBody() ?>

<div id="layout-wrapper" class="h-100">

    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box">
                    <a href="<?= Url::to(['/']) ?>" class="logo">
                                <span class="logo-sm">
                                    <img src="images/logo-small-wh.png" alt="" height="45">
                                </span>
                        <span class="logo-lg">
                                    <img src="images/logo-wh.png" alt="" height="45">
                                </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                    <i class="fa fa-fw fa-bars"></i>
                </button>
            </div>

            <div class="d-flex">
                <div class="d-inline-block d-flex align-items-center">
                    <button id="install" class="btn btn-warning btn-sm me-3 mx-auto" hidden><i class="fas fa-download"></i> Установить приложение</button>
                </div>

                <div class="d-none d-lg-inline-block ms-1">
                    <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                        <i class="bx bx-fullscreen"></i>
                    </button>
                </div>

                <?php if ($isLogged) { ?>
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php $name =  (string)Yii::$app->user->identity ?>
                            <img class="rounded-circle header-profile-user" src="<?= Yii::$app->user->identity->avatar ?>"
                                 alt="<?= $name ?>">
                            <span class="d-none d-xl-inline-block ms-1" key="t-henry"><?= $name ?></span>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="<?= Url::to(['/site/profile']) ?>"><i class="bx bx-user font-size-16 align-middle me-1"></i> <span key="t-profile">Профиль</span></a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="<?= Url::to(['/site/logout']) ?>"><i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span key="t-logout">Выход</span></a>
                        </div>
                    </div>
                <?php } ?>

            </div>
        </div>
    </header>

    <div class="vertical-menu">

        <div class="h-100 overflow-x-auto">
            <?php if ($isLogged) { ?>
                <?= \common\theme\skote\widgets\VerticalMenu::widget() ?>
            <?php } ?>
        </div>
    </div>

    <div class="main-content h-100">

        <div class="page-content h-100">
            <div class="container-fluid h-100 w-100 d-flex flex-column">

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0 font-size-18"><?= \Yii::$app->urlManager->getLastTitle() ?></h4>

                            <div class="page-title-right">
                                <?= \common\theme\skote\widgets\Breadcrumbs::widget(['homeLink' => ['url' => '/', 'label' => '<i class="fa fa-home"></i>']]) ?>
                            </div>

                        </div>
                    </div>
                </div>

                <?= \common\widgets\Flashes::widget() ?>

                <div class="flex-grow-1 overflow-auto">
                <?= $content ?>
                </div>

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
<script type="text/javascript">
    // Проверяем совместимость браузера, в котором мы запускаем
    if ("serviceWorker" in navigator) {
        if (navigator.serviceWorker.controller) {
            console.log("[PWA Builder] active service worker found, no need to register");
        } else {
            //  Регистрация serviceWorker
            navigator.serviceWorker
                .register("/pwa-sw.js", {
                    scope: "/"
                })
                .then(function (reg) {
                    console.log("[PWA Builder] Service worker has been registered for scope: " + reg.scope);
                });
        }
    }
</script>

</body>
</html>
<?php $this->endPage() ?>

