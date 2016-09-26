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

    if(!empty($time)){
        echo '<h2>Time Task</h2>';
        echo "<table border='2px'>";
        foreach ($notAvailable as $key => $date)
        {
            echo "<tr>";
            echo "<td>".$date[0]->format('Y-m-d')."</td>";
            foreach($time as $value){
                if($date[0]->format('Y-m-d') == substr($value, 0, -7)){
                    if(strpos($value, "U") > 0){
                        echo "<td style='background: grey'>".substr($value, 11, -1)."</td>>";
                    }
                    if(strpos($value, "F") > 0){
                        echo "<td style='background: greenyellow'>".substr($value, 11, -1)."</td>>";
                    }
                    if(strpos($value, "B") > 0){
                        echo "<td style='background: red'>".substr($value, 11, -1)."</td>>";
                    }
                }
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    ?>
</div>