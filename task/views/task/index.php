<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use task\models\TimeTask;
?>
<?php $form = ActiveForm::begin();?>
<?= $form->field($model, 'taskBegin')->textInput()->label('Task begin'); ?>
<?= $form->field($model, 'taskEnd')->textInput()->label('Task end'); ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>