<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \yii\jui\DatePicker;


/* @var $this yii\web\View */
/* @var $model backend\models\Task */

$this->params['breadcrumbs'][] = ['label' => 'Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-view">

    <h1>Choose the date</h1>

    <?php
    $form = ActiveForm::begin();
    $model = new \backend\models\Task();
    ?>

    <?= $form->field($model, 'begin')->widget(DatePicker::className(), [
        'dateFormat' => 'yyyy-MM-dd'
    ]);?>

    <?= $form->field($model, 'end')->widget(DatePicker::className(), [
        'dateFormat' => 'yyyy-MM-dd'
    ])?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php
    echo '<h2>Free time</h2>';

    var_dump(1);
    var_dump($free);
    var_dump($busy);
    var_dump($notAvailable);

    /*if($free != null){
        foreach ($free as $key => $value)
        {
            if($key%2 == 0)
            {
                echo 'from: '.$value.' - ';
            }else{
                echo 'to: '.$value.'<br>';
            }
        }
    }*/

    ?>
</div>