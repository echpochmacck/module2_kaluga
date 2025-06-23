<?php

use app\models\Order;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\OrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

   


    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,

        'columns' => [
            [
                'label' => 'good',
                'value' => fn($model) => $model->getProduct()->one()->name
            ],
            [
                'label' => 'email',
                'value' => fn($model) => $model->getUser()->one()->email
            ],
            [
                    'label' => 'price',
                    'value' => fn($model) => $model->getProduct()->one()->price
],
            [
                'label' => 'status',
                'value' => fn($model) => $model->getStatus()->one()->name
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
