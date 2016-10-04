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
        echo "<table border='2px' style='border-collapse: separate'>";
        foreach ($period as $date)
        {
            echo "<tr>";
            echo "<td>".$date->format('Y-m-d')."</td>";
            foreach($time as $value)
            {
                if($date->format('Y-m-d') == $value->begin->format('Y-m-d')){
                    if($value->periodType == 0){
                        echo "<td style='background: grey'>".$value->begin->format('H:i')."</td>>";
                        echo "<td style='background: grey'>".$value->end->format('H:i')."</td>>";
                    }
                    if($value->periodType == 1){
                        echo "<td style='background: greenyellow'>".$value->begin->format('H:i')."</td>>";
                        echo "<td style='background: greenyellow'>".$value->end->format('H:i')."</td>>";
                    }
                    if($value->periodType == 2){
                        echo "<td style='background: red'>".$value->begin->format('H:i')."</td>>";
                        echo "<td style='background: red'>".$value->end->format('H:i')."</td>>";
                    }
                }
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    ?>
</div>