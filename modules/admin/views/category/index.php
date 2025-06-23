<?php

use app\models\Category;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\CategorySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">


    <p>
        <?= Html::a('Create Category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => ['class' => \yii\bootstrap5\LinkPager::class],
        'columns' => [

            'id',
            'name',
            'description',
            [
                    'label' => 'Good quantity',
                    'value' => fn ($model) => $model->count ? $model->count : '0',
],
//            [
//                'class' => ActionColumn::className(),
//                'urlCreator' => function ($action, Category $model, $key, $index, $column) {
//                    if ($action == 'delete') {
//                        if ($model->count) {;
//                            return Url::toRoute([$action, 'id' => $model->id]);
//
//                        } else {
//                            return '';
//                        }
//                    }else {
//                    return Url::toRoute([$action, 'id' => $model->id]);
//                    }
//                 }
//            ],
            [
                'label' => 'actions',
                'value' => fn ($model) => ( $model->count ? '' : Html::a('Delete', ['delete', 'id' => $model->id], ['class' => 'btn btn-danger m-3']))
                    .Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary m-3' ])
                    .Html::a('view', ['view', 'id' => $model->id], ['class' => 'btn btn-success m-3']),
                'format' => 'html'
            ]
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
