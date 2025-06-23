<?php

$defaultFile = '';
if ($model->files) {
    foreach ($model->files as $file) {
        if ($file->default) {
            $defaultFile = $file;
        }
    }
}
?>
<article class="card my-3">
    <div class="card-title bg-secondary bg-gradient p-3 text-light d-flex justify-content-between">
        <h3><?=$model->name?></h3>
        <a href="" title="Delete good"
           class="btn-danger py-1 px-2 justify-content-center text-decoration-none fs-4">☠</a>
    </div>
    <div class="card-body d-flex justify-content-between">
        <div class="card-img">
            <?=$model->files && $defaultFile? \yii\bootstrap5\Html::img('/images/'.$defaultFile->image_url) : ''?>
        </div>
        <div class="card-info">
            <p class="card-text text-black-50"><span class="text-dark">About: </span><?=$model->description?></p>
            <p class="card-text text-danger d-flex justify-content-end"><?=$model->price?> ₽</p>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end">
        <?=\yii\bootstrap5\Html::a('More about', ['view', 'id' => $model->id], ['class'  => 'btn btn-primary fs-3'])?>
    </div>
</article>