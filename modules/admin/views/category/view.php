<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Category;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\Category $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="category-view">


    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?php if (!$products->totalCount):?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    <?php endif?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'description',
        ],
    ]) ?>

    <?php if ($products->totalCount): ?>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => $products,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'description',
            'price',
            [
                'label' => 'actions',
                'value' => fn ($model) =>  Html::a('Delete', ['/admin/product/delete', 'id' => $model->id], ['class' => 'btn btn-danger m-3'])
                    .Html::a('Update', ['/admin/product/update', 'id' => $model->id], ['class' => 'btn btn-primary m-3' ])
                    .Html::a('view', ['/admin/product/view', 'id' => $model->id], ['class' => 'btn btn-success m-3']),
                'format' => 'html'
            ]
        ],
    ]); ?>
    <?php else: ?>

    <h3>В данной категории нет товарвов</h3>
    <?php endif ?>

</div>
