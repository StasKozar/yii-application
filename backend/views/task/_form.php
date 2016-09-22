<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\Task */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'begin')->widget(DateTimePicker::className(), [
        'options' => ['placeholder' => 'Select time'],
        'convertFormat' => true,
        'pluginOptions' => [
            'format' => 'yyyy-MM-dd H:i',
            'todayHighlight' => true
            ]
    ]);?>

    <?= $form->field($model, 'end')->widget(DateTimePicker::className(), [
        'options' => ['placeholder' => 'Select time'],
        'convertFormat' => true,
        'pluginOptions' => [
            'format' => 'yyyy-MM-dd H:i',
            'todayHighlight' => true
        ]
    ])?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
