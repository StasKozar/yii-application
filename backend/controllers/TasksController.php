<?php
/**
 * Created by PhpStorm.
 * User: StasKozar
 * Date: 29/09/2016
 * Time: 15:33
 */

namespace backend\controllers;

use backend\models\Task;
use Yii;
use yii\rest\Controller;
use yii\rest\ActiveController;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

class TasksController extends Controller
{
    public $modelClass = 'backend\models\Task';

    public function actionIndex()
    {
        return Task::find()->all();
    }

    public function actionView($id)
    {
        return Task::findOne($id);
    }

    public function actionSearch($begin, $end)
    {
        if(isset($begin) && isset($end))
        {
            $model = new Task();
            $result = $model->getTime();
            $time = $result['time'];
            $period = $result['period'];
            return [$time, $period];
        }
    }
}