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
    if($free != null){
        /*foreach ($free as $key => $value)
        {
            if($key%2 == 0)
            {
                echo 'from: '.$value.' - ';
            }else{
                echo 'to: '.$value.'<br>';
            }
        }*/
        echo "<table border='1px'>";
        foreach ($notAvailable as $key => $value)
        {
            if($key%2 == 0)
            {
                echo "<tr><td>$value</td>";
            }
            else
            {
                echo "<td>$value</td></tr>";
            }
        }
        echo "</table>";
    }

    ?>
</div>


<!--foreach ($period as $key => $value)
{
if(in_array($value[0]->format('Y-m-d'), $testPeriod))
{
for($i = 0; $i < count($workPeriod); $i++)
{
if ($value[0]->format('Y-m-d H:i') == $workPeriod[$i]['begin']->format('Y-m-d H:i')) {
$freePeriod[] = $workPeriod[$i]['end']->format('Y-m-d H:i');
$freePeriod[] = $value[1]->format('Y-m-d H:i');

} elseif ($value[1]->format('Y-m-d H:i') == $workPeriod[$i]['end']->format('Y-m-d H:i')) {
$freePeriod[] = $value[0]->format('Y-m-d H:i');
$freePeriod[] = $workPeriod[$i]['begin']->format('Y-m-d H:i');

} elseif ($value[0]->format('Y-m-d H:i') == $workPeriod[$i]['begin']->format('Y-m-d H:i')
&& $value[1]->format('Y-m-d H:i') == $workPeriod[$i]['end']->format('Y-m-d H:i')
) {
continue;
} else {
$freePeriod[] = $value[0]->format('Y-m-d H:i');
$freePeriod[] = $workPeriod[$i]['begin']->format('Y-m-d H:i');
$freePeriod[] = $workPeriod[$i]['end']->format('Y-m-d H:i');
$freePeriod[] = $value[1]->format('Y-m-d H:i');

}
}
}
}-->