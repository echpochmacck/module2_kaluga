<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Product $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-view">


    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <article class="card my-3">
        <div class="card-title bg-secondary bg-gradient p-3 text-light d-flex justify-content-between">
            <h3><?=$model->name?></h3>
            <a href="" title="Delete good" class="btn-danger py-1 px-2 justify-content-center text-decoration-none fs-4">☠</a>
        </div>
        <div class="card-body d-flex justify-content-between flex-wrap">
            <?php if ($files): ?>
            <div class="card-img d-flex justify-content-evenly flex-wrap">
            <?php foreach ($files as $file):?>
            <?=Html::img('/images/'.$file->image_url)?>
            <?php endforeach?>
            </div>
            <?php endif ?>

            <div class="card-info">
                <p class="card-text text-black-50"><span class="text-dark">About: </span><?=$model->description?></p>
                <p class="card-text text-danger d-flex justify-content-end"><?=$model->price?> ₽</p>
            </div>
        </div>
    </article>
<!--    --><?php //= DetailView::widget([
//        'model' => $model,
//        'attributes' => [
//            'id',
//            'name',
//            'description',
//            'price',
//            'category_id',
//        ],
//    ]) ?>

</div>
