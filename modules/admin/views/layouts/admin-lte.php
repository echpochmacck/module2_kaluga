<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

\app\assets\AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <nav class="mt-3 d-flex justify-content-between fs-2">

        <?php if (Yii::$app->user->isGuest): ?>
        <div>
            <a href="/admin/default/login" class="login text-decoration-none">Login</a>
        </div>
        <?php else:?>
            <a href="/admin/category" class="text-primary text-decoration-none">Category</a>
            <a href="/admin/product" class="text-primary text-decoration-none">Goods</a>
            <a href="/admin/order" class="text-primary text-decoration-none">Orders</a>
            <form action="/site/logout" method="post">
                <input type="hidden" name="_csrf" value="tQSMXMGa-NSHJR2xwNX3ondFoIrWDFQMQkW4OotwwGvWcrQblt2wpOxpK_yR472aRzfS5Yk1Pj4rct9c2UasJQ=="><button type="submit" class="nav-link btn btn-link logout">Logout (admin@shop.com)</button></form>
        <?php endif;?>
    </nav>
    <h1 class="d-flex justify-content-center pt-3"><?=$this->title?></h1>
</header>

<main id="main" class="flex-shrink-0" role="main">
<main>
    <section class="container">
       <?=$content?>
    </section>
</main>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">&copy; My Company <?= date('Y') ?></div>
            <div class="col-md-6 text-center text-md-end"><?= Yii::powered() ?></div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
