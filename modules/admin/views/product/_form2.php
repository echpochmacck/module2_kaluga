<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Product $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="product-form">

    <?php \yii\widgets\Pjax::begin([
            'enablePushState' => false,
        'enableReplaceState' => false,
        'timeout' => 5000,
        'id' => 'my-pjax'
    ])?>
    <?php $form = ActiveForm::begin(
          [  'id' => 'form-create',

        'options' => ['data-pjax'=> true,]]
    ); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'array_files[]')->fileInput(['multiple' => true, 'id' =>'file-input']) ?>

    <?php if ($productFiles): ?>
        <div class="border border-primary p-3 rounded" id="box">
    <?php foreach ($productFiles as $file): ?>
            <div class="item-img p-3">

            <?=Html::img('/images/'.$file->image_url)?>
            <input type="radio" value="">

        </div>
    <?php endforeach; ?>
        </div>
    <?php endif;?>

    <?= $form->field($model, 'select_img')->hiddenInput()->label(false)?>

    <?= $form->field($model, 'category_id')->dropDownList(
            \app\models\Category::find()->select(['name'])->indexBy('id')->column(),

    ) ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php \yii\widgets\Pjax::end(); ?>
    <?php $this->registerJsFile('js/img.js', ['depends' => 'yii\web\JqueryAsset']); ?>


</div>
